<?php
namespace App\Http\Controllers\partner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\cars;
use Intervention\Image\Facades\Image;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;
use App\Models\master_images;
use App\Models\carImages;
use App\Models\color;
use Helper;
use Session;
use Illuminate\Validation\Rule;

class CarsController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            return view('auth.login');
        }
        $userId = auth()->user()->id;
        $user = User::whereId($userId)->with('roles')->first();
        $role = 'superAdmin';
        if (isset($user->roles[0]->name)) {
            $role = $user->roles[0]->name;
        }
        if (strcmp($role, 'superAdmin') == 0) {
            return redirect()->route('admin.users.list');
        } elseif (strcmp($role, 'partner') == 0) {
            return redirect()->route('partner.booking.list');
        } elseif (strcmp($role, 'agent') == 0) {
            return redirect()->route('agent.booking.list');
        } else {
            Auth::guard('web')->logout();
            return redirect()->route('login');
        }
    }

    public function list()
    {
        $userId = auth()->user()->id;
        $countCars = cars::where('status','NOT LIKE','deleted')->where('user_id','=',$userId)->count();
        $initialCars = cars::where('status','NOT LIKE','deleted')
        ->where('user_id','=',$userId)
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

        return view('partner.cars.list' ,compact('initialCars','countCars'));
    }

    public function loadMoreCars(Request $request)
    {
        $userId = auth()->user()->id;
        $page = $request->input('page');
        $perPage = $request->input('per_page', 10);

        $initialCarIds = $request->input('initial_car_ids', '');

        $initialCarIds = $initialCarIds !== '' ? explode(',', $initialCarIds) : [];

        $cars = cars::where('status','NOT LIKE','deleted')
            ->whereNotIn('id', $initialCarIds)
            ->where('user_id','=',$userId)
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return view('partner.cars.load-more', compact('cars'));
    }

    public function add()
    {
        $userId=auth()->user()->id;
        $colors=color::where('user_id', '=', $userId)->get();
        return view('partner.cars.add',compact('colors'));
    }

    public function store(Request $request)
    {
        $price = $request->price;

        if ($price)
        {
            $numericPart = preg_replace('/[^0-9.]/', '', $price);
            $trimPriceAmount = round($numericPart);
        }
        else
        {
            $trimPriceAmount = 0;
        }
        $messages = [
            'car_name.required' => 'car name is required.',
            'registration_number.required' => 'car number is required.',
            'registration_number.unique' => 'Registration number must be unique',
            'car_type.required' => 'car type is required.',
            'fuel_type.required' => 'fuel type is required.',
            'manufacturing_year.required' => 'manufacturing year is required.',
            'price.required' => 'price is required.',
            'transmission_type.required' => 'transmission is required.',
            'roof_type.required' => 'roof type is required.',
            'plate_type.required' => 'plate type is required.',
        ];

        $validator = Validator::make($request->all(), [
            'car_name' => 'required',
            'registration_number' => [
                'required',
                Rule::unique('cars', 'registration_number')
                    ->where(fn ($query) => $query->
                    whereIn('status', ['available'])),
            ],
            'car_type' => 'required',
            'fuel_type' => 'required',
            'manufacturing_year' => 'required',
            'price' => 'required',
            'transmission_type' => 'required',
            'roof_type' => 'required',
            'plate_type' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

       $userId=auth()->user()->id;

      if(isset($request->photoId) ){
        $uploadedPhotosId=$request->photoId;
      }


        $cars = cars::create([
            'name'=>strtolower($request->car_name),
            'registration_number'=>$request->registration_number,
            'transmission'=>$request->transmission_type,
            'fuel_type'=>$request->fuel_type,
            'manufacturing_year'=>$request->manufacturing_year,
            'car_type'=>$request->car_type,
            'roof_type'=>$request->roof_type,
            'price'=>$trimPriceAmount,
            'user_id'=>$userId,
           ]);


        $carId= $cars->id;

         if($carId){
             $this->insertOrUpdateCarsmeta($carId,'plate_type',$request->plate_type);
                    if(isset($request->photoId) && !empty($uploadedPhotosId))
                    {
                        foreach($uploadedPhotosId as $photosId){
                            carImages::create(['imageId'=>$photosId,'carId'=>$carId,'status'=>'active','featured'=>'not_set']);
                        }
                         $featuredCarId = $request->featured_check;
                          if ($featuredCarId !== null)
                           {
                              carImages::where('imageId', $featuredCarId)->update([
                                  'imageId'=>$featuredCarId,
                                  'carId'=>$carId,
                                  'status'=>'active',
                                  'featured'=>'set'
                              ]);
                          }
                    }

         }

         return redirect()->route('partner.car.list')->with('success', 'Car has been added');
    }


    public function uploadImage(Request $request)
    {
        $userId = auth()->user()->id;
        $imageObjects = [];

        $validator = Validator::make($request->all(), [
            'car_photos.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all(), 'success' => false, 'error' => true]);
        }

        foreach ($request->file('car_photos') as $image) {
            $nameWithExtension = time() . '-' . $image->getClientOriginalName();
            $name = str_replace('.'.$image->getClientOriginalExtension(), "", $nameWithExtension);
            $extension = $image->getClientOriginalExtension();
            // dd($extension);

            $image->move(public_path() . '/uploads/CarPhotos/' . $userId . '/', $name);

            $originalImage = Image::make(public_path() . '/uploads/CarPhotos/' . $userId . '/' . $name);

            // Full size - 1920 width, soft crop
            $originalImage->resize(1920, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/' . $name . '-1920.' . $extension);

            // 1024 width - soft crop
            $originalImage->resize(1024, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/' . $name . '-1024.' . $extension);

            // 512 width - soft crop
            $originalImage->resize(512, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/' . $name . '-512.' . $extension);
            // 300 width - soft crop
            $originalImage->resize(300, null, function ($constraint) {

                $constraint->aspectRatio();
            });
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/' . $name . '-300.' . $extension);

            // 512x512 - hard crop
            $originalImage->fit(512, 512);
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/' . $name . '-512x512.' . $extension);

            // 150x150 - hard crop
            $originalImage->fit(150, 150);
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/' . $name . '-150x150.' . $extension);

            $numericUUID = $this->generateNumericUUID();

            $imageObject = master_images::create([
                'name' => $name,
                'original_name' => $image->getClientOriginalName(),
                'url' => $this->getBaseUrlSystem().'/uploads/CarPhotos/' . $userId . '/' . $name . '-300.' . $extension,
                'base_url' => public_path() . '/uploads/CarPhotos/' . $userId . '/' . $name . '-300.' . $extension,
                'ImageUniqueId' => $numericUUID
            ]);

            $imageObjects[] = $imageObject;
        }

        return response()->json(['success' => true, 'error' => false, 'imageId' => $imageObject->id, 'ImageUniqueId' => $imageObject->ImageUniqueId, 'url'=>$imageObject->url]);
    }

    private function generateNumericUUID()
    {
        $uuid = '';
        for ($i = 0; $i < 13; $i++) {
            $digit = rand(0, 9);
            $uuid .= $digit;
        }
        return $uuid;
    }

    public function ajaxFilter(Request $request)
    {
            $userId=auth()->user()->id;

            $carIds= $request->carIds;
            if(isset($request->orderBy) && isset($request->carName)){
                $cars=cars::where('user_id','=',$userId)->where('name','LIKE',$request->carName)->where('status','NOT LIKE','deleted')->orderBy('price',$request->orderBy)->get();
            }
            else{

                if(isset($request->orderBy)){
                    $order = $request->orderBy;

                    if(strcmp($order,'asc')==0){
                        $cars= cars::where('user_id','=',$userId)->whereIn('id',$carIds)->orderBy('price',$order)->where('status','NOT LIKE','deleted')->get();
                    }
                    else{
                        $cars= cars::where('user_id','=',$userId)->whereIn('id',$carIds)->orderBy('price',$order)->where('status','NOT LIKE','deleted')->get();


                    }
                }
                if(isset($request->carName)){

                    $cars= cars::where('user_id','=',$userId)->whereIn('id',$carIds)->where('name','LIKE',$request->carName)->orderBy('created_at','DESC')->where('status','NOT LIKE','deleted')->get();
                }
            }

            if($cars){
                $cars_list = '';
                foreach ($cars as $car) {
                    $thumbnails = Helper::getFeaturedSetCarPhotoById($car->id);
                    $carImageUrl = '';
                        if (count($thumbnails) >0)
                        {
                            foreach($thumbnails as $thumbnail)
                            {
                                if(strcmp($thumbnail->featured,'set')==0)
                                {
                                    $featuredImage=Helper::getPhotoById($thumbnail->imageId);
                                    $featureImageUrl=$featuredImage->url;
                                }
                            }
                        }
                        else{
                                $featureImageUrl = asset('images/no_image.svg');
                        }

                        $modifiedImgUrl = $featureImageUrl ?? '';
                    $modifiedImgUrl = $featureImageUrl;
                    $cars_list .= '
                    <div data-car-id="' . $car->id . '" data-href="'. route('partner.car.view', $car->id) .'" class="matchBox car_row_main py-3 border border-transparent clickable_content booking_img tab:py-3 tab:w-1/2">
                        <div class="bg_car_info relative flex items-center border border-transparent car_box">
                            <div class="w-2/5 tab:w-1/2">
                                <div  data-href="'. route('partner.car.view', $car->id) .'" class="upload_Height relative flex flex-col items-center main_image_content justify-center  border border-transparent  booking_inner_b ">
                                    <p class="absolute text-xs font-normal text-purple left-2 top-1">'.$car->registration_number.'</p>
                                    <img class="adj_image_size" src="' . $modifiedImgUrl . '" alt="' . $car->name .' car">
                                </div>
                            </div>
                            <div class="w-3/5 px-4 tab:w-1/2">
                                <div class="text-left">
                                    <div>
                                        <p class="car_name text-sm sm:text-base text-midgray font-normal mb-1 text-[#898376]">' . $car->name . '</p>
                                    </div>
                                    <div class="car_details text-midgray font-normal text-[#898376] mb-1">
                                        ' . (strcmp($car->roof_type, 'yes') == 0 ? '<span>Roof Type</span>' : '') . '
                                        <span>' . $car->fuel_type . '</span>
                                    </div>
                                    <div class="price_section">
                                        <p class="pb-1 text-sm font-normal text-black sm:text-base">₹ ' . number_format($car->price, 2) . ' per day</p>
                                    </div>
                                    <a href="'.route('partner.car.view',$car->id).'" class="inline-flex items-center py-1 pr-3 text-sm font-normal text-black">View Details<span class="ml-4"><img src="' . asset('images/view_arrow.svg') . '" alt="arrow icon"></span></a>
                                </div>
                                <div class="absolute right-4 top-3">
                                    <a href="javascript:void(0)" data-delete-url="'.route('partner.car.delete',$car->id).'" class="delete_btn" >
                                    <span class="">
                                    <img src="'.asset('images/delete_icon.svg').'" class="delete_icon" alt="delete_icon">
                                    </span>
                                    </a>
                               </div>

                            </div>
                        </div>
                    </div>';
                }

                return response()->json(['success' => true, 'error' => false, 'car_list' => $cars_list]);
            }
            else{
                return response()->json(['success' => false, 'error' => true,'car_list'=>'']);

            }
    }

    /**
     * Display the specified resource.
     */

    public function view($id)
    {
        if(!$id)
        {
            return redirect()->route('partner.car.list');
        }
        else
        {
             $userId=auth()->user()->id;
             $car = cars::where('user_id', $userId)
            ->where('status', '!=', 'deleted')
            ->where('id', $id)
            ->first();

            if (!$car) {
                return redirect()->route('partner.car.list');
            }

            return view('partner.cars.view',compact('car'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit($id){
        if(!$id){
            return redirect()->route('partner.car.list');
        }else{

             $userId=auth()->user()->id;

             $cars = cars::where('user_id', $userId)
            ->where('status', '!=', 'deleted')
            ->where('id', $id)
            ->first();

            if (!$cars) {
                return redirect()->route('partner.car.list');
            }

            return view('partner.cars.edit',compact('cars'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $price = $request->price;

        if ($price) {
            $numericPart = preg_replace('/[^0-9.]/', '', $price);
            $trimPriceAmount = round($numericPart);
        }
        else{
            $trimPriceAmount = 0;
        }

        $messages = ['car_name.required' => 'car name is required.',
        'car_type.required' => 'car type is required.',
        'fuel_type.required'=>'fuel type is required.',
        'manufacturing_year.required'=>'manufacturing year is required.',
        'price.required'=>'price is required.',
        'transmission_type.required'=>'transmission is required.',
        'roof_type.required'=>'roof type is required.',
        'plate_type.required'=>'plate type is required.',
        ];


        $validator = Validator::make($request->all(), [
           'car_name' => 'required',
           'car_type' => 'required',
           'fuel_type' => 'required',
           'manufacturing_year' => 'required',
           'price' => 'required',
           'transmission_type' => 'required',
           'roof_type' => 'required',
           'plate_type' => 'required',
        ], $messages);

       if ($validator->fails()) {
           return redirect()
               ->back()
               ->withInput()
               ->withErrors($validator)
               ->with('error', 'Validation error. Please correct the errors and try again.');
       }
       $userId=auth()->user()->id;

      if(isset($request->photoId)){
        $uploadedPhotosId=$request->photoId;
      }

      $cars = cars::whereId($id)->update([
          'name'=>strtolower($request->car_name),
          'transmission'=>$request->transmission_type,
          'fuel_type'=>$request->fuel_type,
          'manufacturing_year'=>$request->manufacturing_year,
          'car_type'=>$request->car_type,
          'roof_type'=>$request->roof_type,
          'price'=>$trimPriceAmount,
          'user_id'=>$userId,
        ]);

          carImages::where('carId','=',$id)->delete();

         if($id){

            $this->insertOrUpdateCarsmeta($id,'plate_type',$request->plate_type);

              if(isset($request->photoId) && !empty($uploadedPhotosId)) {

                 foreach($uploadedPhotosId as $photosId)
                        {
                            carImages::create(['imageId'=>$photosId,'carId'=>$id,'status'=>'active','featured'=>'not_set']);
                        }
                    $featuredCarId = $request->featured_check;
                    if ($featuredCarId !== null) {
                        carImages::where('imageId', $featuredCarId)->update([
                            'imageId'=>$featuredCarId,
                            'carId'=>$id,
                            'status'=>'active',
                            'featured'=>'set'
                        ]);
                    }
                }
         }
         return redirect()->route('partner.car.view',$id)->with('success', 'car details has been updated');
    }

    public function delete(Request $request, $id)
    {
        if($id){
            cars::whereId($id)->delete();
            return response()->json(['success'=>true,'error'=>false,'msg'=>__('Car has been deleted')]);
        }
        else{
            return response()->json(['success'=>true,'error'=>false,'msg'=>__('Car not deleted')]);
        }

    }

    public function uploadImageDelete(Request $request, $id)
    {
        if($id){
            carImages::where('imageId','=',$id)->delete();
            return response()->json(['success'=>true,'error'=>false,'msg'=>__('Car has been deleted')]);
        }
        else{
            return response()->json(['success'=>false,'error'=>true,'msg'=>__('Car not deleted')]);
        }

    }

}
