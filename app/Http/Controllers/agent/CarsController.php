<?php
namespace App\Http\Controllers\agent;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Helper;
use App\Models\cars;
use App\Models\shareCars;
use App\Models\carImages;
use App\Models\master_images;
use Illuminate\Support\Facades\Validator;
class CarsController extends Controller
{
    public function __construct(){
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

        $agentId = auth()->user()->id;

        $get_car_id = shareCars::where('agent_id', '=', $agentId)->pluck('car_id');

        $partners = cars::whereIn('id', $get_car_id)
        ->where('status', 'NOT LIKE', 'deleted')
        ->orderBy('created_at', 'desc')
        ->pluck("user_id")
        ->unique();


        $partnerIds = User::where('id', '!=', auth()->user()->id)
        ->where('uniqueUserId', '=', auth()->user()->id)
        ->where('status', 'NOT LIKE', 'inactive')
        ->orderBy('created_at', 'desc')
        ->pluck('id')
        ->toArray();

        $countCars = cars::whereIn('id', $get_car_id)->where('status', 'NOT LIKE', 'deleted')
        ->count();

        $initialCars = cars::whereIn('id', $get_car_id)->where('status', 'NOT LIKE', 'deleted')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

        return view('agent.cars.list', compact('partners', 'initialCars','countCars'));
    }

    public function loadMoreCars(Request $request)
    {
        //
        $agentId = auth()->user()->id;
        $get_car_id = shareCars::where('agent_id', '=', $agentId)->pluck('car_id');
        //

        $partnerIds = User::where('id', '!=', auth()->user()->id)
        ->where('uniqueUserId', '=', auth()->user()->id)
        ->where('status', 'NOT LIKE', 'inactive')
        ->orderBy('created_at', 'desc')
        ->pluck('id')
        ->toArray();

        $page = $request->input('page');
        $perPage = $request->input('per_page', 10);

        $initialCarIds = $request->input('initial_car_ids', '');

        $initialCarIds = $initialCarIds !== '' ? explode(',', $initialCarIds) : [];

        $cars = cars::whereIn('id', $get_car_id)->where('status', 'NOT LIKE', 'deleted')
            ->whereNotIn('id', $initialCarIds)
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return view('agent.cars.load-more', compact('cars'));
    }

    public function partner_search(Request $request)
    {
        $adminId=auth()->user()->id;
        $partners = User::where('id','!=',auth()->user()->id)
        ->where('uniqueUserId', '=', auth()->user()->id)
        ->where('status', 'NOT LIKE', 'inactive')
        ->whereHas(
        'roles', function($q){
            $q->where('name','LIKE', 'partner');
        }
        )
        ->with('roles')
        ->orderBy('created_at','Desc')->get();
        $partnerIds = User::where('id', '!=', auth()->user()->id)
        ->where('uniqueUserId', '=', auth()->user()->id)
        ->where('status', 'NOT LIKE', 'inactive')
        ->orderBy('created_at', 'desc')
        ->pluck('id')
        ->toArray();
        $partnerId = $request->partnerId;

        $cars = cars::whereIn('user_id', $partnerIds)
                 ->where('status', 'NOT LIKE', 'deleted')
                 ->orderBy('created_at', 'desc')
                ->get();


        if($cars){
            $cars_list = '';
            foreach ($cars as $car) {
                $thumbnails = Helper::getFeaturedSetCarPhotoById($car->id);
                $carImageUrl = asset('images/no_image.svg');
                foreach ($thumbnails as $thumbnail) {
                    $image = Helper::getPhotoById($thumbnail->imageId);
                    $carImageUrl = $image->url;
                }

                $modifiedImgUrl = $carImageUrl;

            $cars_list .= '
            <div data-car-id="' . $car->id . '" data-href="'. route('agent.car.view', $car->id) .'" class="loop_list_cr border border-transparent clickable_content booking_img tab:py-3 w-1/2 lg:w-full">
            <div class="bg_car_info relative flex items-center border border-transparent car_box">
                <div class="w-2/5 tab:w-1/2">
                    <div class="upload_img_p relative main_image_content border border-transparent  booking_inner_b adj_height_book_inner">
                        <p class="regist_number text-xs font-normal text-purple">'.$car->registration_number.'</p>
                        <div class="upload_Height">
                        <img class="adj_image_size" src="' . $modifiedImgUrl . '" alt="' . $car->name .' car">
                        </div>
                    </div>
                </div>
                <div class="img_ct_info_cr w-3/5 tab:w-1/2">
                    <div class="text-left">
                        <div class="tp_name_car">
                            <p class="car_name ad_car_name text-sm sm:text-base text-midgray font-normal text-[#898376]">' . $car->name . '</p>

                        <div class="car_details ad_car_name text-midgray font-normal text-[#2B2B2B]">
                            ' . (strcmp($car->roof_type, 'yes') == 0 ? '<span>Roof Type</span>' : '') . '
                            <span> ' . $car->fuel_type . '</span>
                        </div>
                        </div>
                        <div class="bt_details_or">
                        <div class="price_section">
                            <p class="pb-1 text-sm font-normal text-[#272522] sm:text-base">₹ ' . number_format($car->price, 2) . ' per day</p>
                        </div>

                        <a href="'.route('agent.car.view',$car->id).'" class="inline-flex items-center py-1 pr-3 text-sm font-normal text-[#342B18]">View Details<span class="ml-4"><img src="' . asset('images/view_arrow.svg') . '" alt="arrow icon"></span></a>
                    </div>
                    </div>
                    <div class="absolute right-4 top-5">
                        <a href="javascript:void(0)" data-delete-url="'.route('agent.car.delete',$car->id).'" class="delete_btn" >
                        <span class="">
                        <img src="'.asset('images/delete_icon_red.svg').'" class="delete_icon" alt="delete_icon">
                        </span>
                        </a>
                   </div>

                   <div class="absolute right-4 bottom-5">
                        <a href="'.route('agent.car.edit',$car->id).'" class="edit_btn" >
                        <span class="">
                        <img src="'.asset('images/edit_icon_black.svg').'" class="edit_icon" alt="edit_icon">
                        </span>
                        </a>
                   </div>

                </div>
            </div>
        </div>';
            }
        }

        return response()->json(['success' => true,'error' => false,'cars_list'=> $cars_list,'msg'=>'Partner has been retrieved']);
    }

    public function add()
    {
        $partners = User::where('id','!=',auth()->user()->id)
        ->where('uniqueUserId', '=', auth()->user()->id)
        ->where('status', 'NOT LIKE', 'inactive')
        ->whereHas(
        'roles', function($q){
            $q->where('name','LIKE', 'partner');
        }
        )
        ->with('roles')
        ->orderBy('created_at','Desc')->get();

        return view('agent.cars.add',compact('partners'));
    }

    public function store(Request $request)
    {
        $price = $request->price;
        if ($price) {
            $numericPart = preg_replace('/[^0-9.]/', '', $price);
            $trimPriceAmount = round($numericPart);
        }
        else{
            $trimPriceAmount = 0;
        }

       $messages = [
        'car_name.required' => 'Car name is required',
        'registration_number.required' => 'Registration number is required',
        'registration_number.unique' => 'Registration number must be unique',
        'car_type.required' => 'Car type is required',
        'fuel_type.required'=>'fuel type is required.',
        'manufacturing_year.required'=>'Manufacturing year is required',
        'price.required'=>'Price is required',
        'transmission_type.required'=>'Transmission is required',
        'roof_type.required'=>'Roof type is required',
        'plate_type.required'=>'Plate type is required',
       ];
       $validator = Validator::make($request->all(), [
           'car_name' => 'required',
           'registration_number' => 'required|unique:cars',
           'car_type' => 'required',
           'fuel_type' => 'required',
           'manufacturing_year' => 'required',
           'price' =>  'required',
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

        $userId=$request->partner_id;

        if(isset($request->photoId)){
          $uploadedPhotosId=$request->photoId;
        }

        $cars = cars::create([
            'user_id'=>$userId,
            'name'=>strtolower($request->car_name),
            'registration_number'=>$request->registration_number,
            'transmission'=>$request->transmission_type,
            'fuel_type'=>$request->fuel_type,
            'manufacturing_year'=>$request->manufacturing_year,
            'car_type'=>$request->car_type,
            'roof_type'=>$request->roof_type,
            'price'=>$trimPriceAmount,
           ]);
           $carId= $cars->id;

           if($carId){
            $this->insertOrUpdateCarsmeta($carId,'plate_type',$request->plate_type);
               if(isset($request->photoId) && !empty($uploadedPhotosId)) {
                        foreach($uploadedPhotosId as $photosId)
                          {
                              carImages::create(['imageId'=>$photosId,'carId'=>$carId,'status'=>'active','featured'=>'not_set']);
                          }
                          $featuredCarId = $request->featured_check;
                          if ($featuredCarId !== null) {
                              carImages::where('imageId', $featuredCarId)->update([
                                  'imageId'=>$featuredCarId,
                                  'carId'=>$carId,
                                  'status'=>'active',
                                  'featured'=>'set'
                              ]);
                          }


                  }
           }

         return redirect()->route('agent.car.list')->with('success', 'Car has been added');
    }

    public function uploadImage(Request $request){
        $userId = auth()->user()->id;
        $data = $request->all();
        $imageObjects = [];

        foreach ($request->car_photos as $image) {
            $name = $image->getClientOriginalName();
            $mimeType = $image->getMimeType();
            $name = time() . $name;

            if (!file_exists(public_path() . '/uploads/')) {
                mkdir(public_path() . '/uploads/');
            }
            if (!file_exists(public_path() . '/uploads/CarPhotos')) {
                mkdir(public_path() . '/uploads/CarPhotos');
            }
            if (!file_exists(public_path() . '/uploads/CarPhotos/' . $userId)) {
                mkdir(public_path() . '/uploads/CarPhotos/' . $userId);
            }

            // Move original image
            $image->move(public_path() . '/uploads/CarPhotos/' . $userId . '/', $name);

            // Open original image
            $originalImage = Image::make(public_path() . '/uploads/CarPhotos/' . $userId . '/' . $name);

            // Full size - 1920 width, soft crop
            $originalImage->resize(1920, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/1920_'.$name);

            // 1024 width - soft crop
            $originalImage->resize(1024, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/1024_'.$name);

            // 512 width - soft crop
            $originalImage->resize(512, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/512_'.$name);

            // 512x512 - hard crop
            $originalImage->fit(512, 512);
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/512x512_'.$name);

            // 150x150 - hard crop
            $originalImage->fit(150, 150);
            $originalImage->save(public_path() . '/uploads/CarPhotos/' . $userId . '/150x150_'.$name);

            function generateNumericUUID() {
                $uuid = '';
                for ($i = 0; $i < 13; $i++) {
                    $digit = rand(0, 9);
                    $uuid .= $digit;
                }
                return $uuid;
            }

            $numericUUID = generateNumericUUID();
            $imageObject = master_images::create([
                'name' => $name,
                'original_name' => $image->getClientOriginalName(),
                'url' => url('/uploads/CarPhotos/' . $userId . '/512_' . $name),
                'base_url' => public_path() . '/uploads/CarPhotos/' . $userId . '/512_' . $name,
                'ImageUniqueId' => $numericUUID
            ]);

            // $imageObjects[] = $imageObject;
            // echo "<pre>";
            // print_r($imageObject);
            // die;
        }

        return response()->json(['success' => true, 'error' => false, 'imageId' => $imageObject->id, 'ImageUniqueId' => $imageObject->ImageUniqueId, 'url'=>$imageObject->url]);

    }


    public function view($id){
        if (!$id) {
            return redirect()->route('agent.cars.list');
        }
        else {
            $car = cars::where('status', 'NOT LIKE', 'deleted')->find($id);

            if (!$car) {
                return redirect()->route('agent.cars.list')->with('error', 'Car not found.');
            }
            $partnerId = $car->user_id;
            return view('agent.cars.view', compact('car','partnerId'));
        }
    }

    // public function view($id)
    // {

    //     if(!$id){
    //         return redirect()->route('agent.car.list');
    //     }else{
    //         $partners = User::where('id','!=',auth()->user()->id)
    //         ->where('uniqueUserId', '=', auth()->user()->id)
    //         ->where('status', 'NOT LIKE', 'inactive')
    //         ->whereHas(
    //         'roles', function($q){
    //             $q->where('name','LIKE', 'partner');
    //         }
    //         )
    //         ->with('roles')
    //         ->orderBy('created_at','Desc')->get();

    //         $partnerIds = $partners->pluck('id');

    //          $car = cars::whereIn('user_id', $partnerIds)
    //         ->where('status', '!=', 'deleted')
    //         ->where('id', $id)
    //         ->first();

    //         if (!$car || !in_array($car->user_id, $partnerIds->toArray())) {
    //             return redirect()->route('agent.car.list');
    //         }
    //         $partnerId = $car->user_id;
    //         return view('agent.cars.view',compact('car','partnerId'));
    //     }
    // }

    public function edit($id)
    {
        if(!$id){
            return redirect()->route('agent.car.list');
        }else{
            $partners = User::where('id','!=',auth()->user()->id)
            ->where('uniqueUserId', '=', auth()->user()->id)
            ->where('status', 'NOT LIKE', 'inactive')
            ->whereHas(
            'roles', function($q){
                $q->where('name','LIKE', 'partner');
            }
            )
            ->with('roles')
            ->orderBy('created_at','Desc')->get();

            $partnerIds = $partners->pluck('id');

             $cars = cars::whereIn('user_id', $partnerIds)
            ->where('status', '!=', 'deleted')
            ->where('id', $id)
            ->first();

            if (!$cars || !in_array($cars->user_id, $partnerIds->toArray())) {
                return redirect()->route('agent.car.list');
            }

            return view('agent.cars.edit',compact('cars','partners'));
        }
    }

    public function update(Request $request, string $id){
        $price = $request->price;
        if ($price) {
            $numericPart = preg_replace('/[^0-9.]/', '', $price);
            $trimPriceAmount = round($numericPart);
        }
        else{
            $trimPriceAmount = 0;
        }

        $messages = ['car_name.required' => 'Car name is required',
        'car_type.required' => 'Car type is required',
        'fuel_type.required'=>'fuel type is required',
        'manufacturing_year.required'=>'Manufacturing year is required',
        'price.required'=>'Price is required',
        'transmission_type.required'=>'Transmission is required',
        'roof_type.required'=>'Roof type is required',
         'plate_type.required'=>'Plate type is required',
        ];


        $validator = Validator::make($request->all(), [
           'partner_id' => 'required',
           'car_name' => 'required',
           'car_type' => 'required',
           'fuel_type' => 'required',
           'manufacturing_year' => 'required',
           'price' =>  'required',
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
       $userId=$request->partner_id;
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
                 foreach($uploadedPhotosId as $photosId){
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
         return redirect()->route('agent.car.view',$id)->with('success', 'car details has been updated');
    }

    public function delete(Request $request, $id){
        if($id){
            cars::whereId($id)->update(['status'=>'deleted']);
            return response()->json(['success'=>true,'error'=>false,'msg'=>__('Car has been deleted')]);
        }
        else{
            return response()->json(['success'=>false,'error'=>true,'msg'=>__('Car not deleted')]);
        }
    }

    public function uploadImageDelete(Request $request, $id)
    {
        if($id){
            carImages::where('imageId','=',$id)->update(['status'=>'inactive']);
            return response()->json(['success'=>true,'error'=>false,'msg'=>__('Car has been deleted')]);
        }
        else{
            return response()->json(['success'=>false,'error'=>true,'msg'=>__('Car not deleted')]);
        }

    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function ajaxShowCars(Request $request)
    {
$agentId = auth()->user()->id;

        $userIds = cars::where('user_id','=',$request->partner_id)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')->pluck('user_id');



         $carIds = cars::whereIn('user_id',$userIds)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')->pluck('id');


         $sharedCarId = shareCars::whereIn('car_id',$carIds)->where('agent_id','=',$agentId)->pluck('car_id');


         $cars = cars::whereIn('id', $sharedCarId)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')
        ->get();

        //  echo "<pre>";
        //  print_r($cars->toArray());
        //  die;


        // $adminId = auth()->user()->id;


        // $initialCars = cars::whereIn('id', $get_car_id)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')
        // ->get();

        // $partners = User::where('id','!=',auth()->user()->id)
        // ->where('status', 'NOT LIKE', 'deleted')
        // ->whereHas(
        // 'roles', function($q){
        //         $q->where('name','LIKE', 'partner');
        //     }
        // )
        // ->with('roles')
        // ->orderBy('created_at','Desc')->get();

        // $partnerId = $request->partnerId;

        // $cars = cars::where('user_id', '=', $partnerId)->orderBy('created_at', 'desc')->get();

        if($cars)
        {
            $cars_list = '';
            foreach ($cars as $car) {
                $thumbnails = Helper::getFeaturedSetCarPhotoById($car->id);
                $carImageUrl = asset('images/no_image.svg');
                foreach ($thumbnails as $thumbnail) {
                    $image = Helper::getPhotoById($thumbnail->imageId);
                    $carImageUrl = $image->url;
                }
                $modifiedImgUrl = $carImageUrl;

                $cars_list .= '
                    <div class="booking_img w-1/2 px-4 md:w-full mb-7" data-car-id="' . $car->id . '" data-href="' . route('agent.car.view', $car->id) . '">
                        <div class="relative px-5 py-4 bg-white rounded-[9px]">
                            <div class="flex py-3">
                                <div class="w-2/5">
                                    <div class="flex items-center justify-center ">
                                        <img class="block w-[240px] h-[120px] m-auto object-contain " src="' . $modifiedImgUrl . '" alt="' . $car->name . ' car">
                                    </div>
                                </div>
                                <div class="w-3/5">
                                    <div class="h-full xl:pl-4 xl:pr-4 pl-10 pr-6">
                                        <div class="flex flex-col ">
                                            <h3 class="text-base font-medium leading-4 text-[#2B2B2B] pb-1">' . ucwords($car->name) . '</h3>
                                            <p class="text-sm font-normal leading-none text-[#2B2B2B] pb-1.5 last:pb-0">' . strtoupper($car->registration_number) . '</p>
                                            <p class="capitalize text-sm font-medium leading-none text-[#2B2B2B] pb-1.5 last:pb-0">' . Helper::getUserMetaByCarId($car->id, 'company_name') . '</p>
                                            <div class="block py-4">
                                                <p class="text-sm font-medium leading-none text-[#272522]">₹ ' . number_format($car->price, 0, '.', ',') . ' per day</p>
                                            </div>
                                            <div class="w-full">
                                                <a class="inline-block" href="' . route('agent.car.view', $car->id) . '">
                                                    <div class="flex items-center">
                                                        <div class="w-auto">
                                                            <span class="text-[13px] font-normal leading-none text-[#342B18]">View Details</span>
                                                        </div>
                                                        <div class="pl-4 min-w-12">
                                                            <img src="' . asset('images/right_arrow_cars_page.svg') . '" alt="arrow icon">
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
            }
        }

        return response()->json(['success' => true,'error' => false,'cars_list'=> $cars_list,'msg'=>'Partner has been retrieved']);

    }






}
