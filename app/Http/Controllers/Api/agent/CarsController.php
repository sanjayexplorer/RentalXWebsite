<?php
namespace App\Http\Controllers\Api\agent;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Helper;
use App\Models\cars;
use App\Models\shareCars;
use App\Models\carImages;
use App\Models\master_images;
use App\Models\UserMetas;
use App\Models\carsMeta;
use Illuminate\Support\Facades\Validator;
class CarsController extends Controller
{
  
    public function list(Request $request)
    {
    
        $agentId = auth()->user()->id;

        $get_car_id = shareCars::where('agent_id', '=', $agentId)->pluck('car_id');
 
        $partners = cars::whereIn('id', $get_car_id)
                ->where('status', 'NOT LIKE', 'deleted')
                ->orderBy('created_at', 'desc')
                ->pluck("user_id")
                ->unique();

        // Attach company names to the partner IDs
        $partnersWithIdCompanyName = $partners->map(function ($userId) {
            // Fetch the company name of the partner owner
            $companyMeta = UserMetas::where([
                ['userId', '=', $userId],
                ['meta_key', 'LIKE', 'company_name']
            ])->first();

            // Return an array or object with the user ID and company name
            return (object)[
                'id' => $userId,
                'companyName' => $companyMeta ? $companyMeta->meta_value : ''
            ];
        });

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

        // Getting the car ids to fetch related thumbnails
        // $carIds = $initialCars->pluck('id')->toArray();

        // Optimized thumbnail query
        $thumbnails = carImages::whereIn('carId', $initialCars->pluck('id')->toArray())
        ->where('featured', 'set')
        ->get()
        ->keyBy('carId');
        
        // Attach the feature image URLs to the cars
        $initialCars = $initialCars->map(function ($car) use ($thumbnails) {
                $thumbnail = $thumbnails->get($car->id);
                $featuredImage = $thumbnail ? Helper::getPhotoById($thumbnail->imageId) : null;
                $car->featureImageUrl = $featuredImage ? $featuredImage->url : null;
                return $car;
        });

        if(!$initialCars){
            return response()->json(['success'=>false,'error'=>true], 404);
        }

        return response()->json([
        'success'=>true,'error'=>false, 
        'cars' => $initialCars, 
        'totalCount' => $countCars, 
        'partnersWithIdCompanyName' => $partnersWithIdCompanyName, 
            // 'partnerIds' => $partnerIds, 
            // 'partnersDetail' => $partnersDetail, 
        ], 200);

    }

    public function loadMoreCars(Request $request)
    {

        // return response()->json(['success'=>true,'error'=>false, 'cars' => $request->toArray(), ]);
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
            // ->whereNotIn('id', $initialCarIds) 
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();


        // Optimized thumbnail query
        $thumbnails = carImages::whereIn('carId',$cars->pluck('id')->toArray())
                ->where('featured', 'set')
                ->get()
                ->keyBy('carId');
                
        // Attach the feature image URLs to the cars
        $cars = $cars->map(function ($car) use ($thumbnails) {
                        $thumbnail = $thumbnails->get($car->id);
                        $featuredImage = $thumbnail ? Helper::getPhotoById($thumbnail->imageId) : null;
                        $car->featureImageUrl = $featuredImage ? $featuredImage->url : null;
                        return $car;
        });

        //     return response()->json(['success'=>true,'error'=>false,
        //     'agentId'=>$agentId,
        //     'get_car_id'=>$get_car_id,
        //     'partnerIds'=>$partnerIds,
        //     'page'=>$page,
        //     'initialCarIds'=>$initialCarIds,
        //     'cars'=>$cars,
        //     // 'get_car_id'=>$get_car_id,
        //     // 'get_car_id'=>$get_car_id,
        //     // 'cars' => $request->toArray(),
        //  ]);

        if(!$cars){
           return response()->json(['success'=>false,'error'=>true, ], 404);
        }
       
        return response()->json(['success'=>true,'error'=>false, 'cars' => $cars, ]);

    }

    public function imageUrl(Request $request,$id)
    {
    
        $carPhoto = carImages::where('carId', '=', $id)->where('featured', 'set')->first();
        $carImageUrl = asset('public/images/no_image.svg');
        if (!$carPhoto) {
            return response()->json(['success' => false, 'error' => true], 404);
        }
        
        $image = Helper::getPhotoById($carPhoto->imageId);
        if ($image) {
           $carImageUrl = $image->url;
        }

        return response()->json(['success' => true, 'error' => false, 'imageUrl' =>$carImageUrl, 'carPhoto' => $carPhoto]);
    }

    public function fetchThumbnails(Request $request,$carId)
    {
        try {
            $car = cars::findOrFail($carId);
            $thumbnails = carImages::where('carId', $carId)->get();
            $carImageUrls = [];
            foreach ($thumbnails as $thumbnail) {
                $image = master_images::whereId($thumbnail->imageId)->first();
                if ($image) { 
                    $carImageUrls[] = [ 'url' => $image->url, 'featured' => $thumbnail->featured ]; 
                }
            }
            return response()->json(['success' => true, 'error' => false, 'modifiedImgUrls' => $carImageUrls ], 200);

        } catch (\Exception $e) {
            return response()->json([ 'success' => false, 'error' => true, ], 404);
        }
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

           if (!$cars) {
            return response()->json(['success' => false, 'error' => true],404);
        }

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

           return response()->json(['success' => true, 'error' => false, 'carId' => $carId], 200);
    }

    public function view(Request $request,$id)
    {
        if (!$id) {
            return response()->json([ 'success' => false, 'error' => true, ], 400);
        } 
        else {
            $car = cars::where('status', 'NOT LIKE', 'deleted')->find($id);

            $plate_type = carsMeta::where([['carId', '=', $id], ['meta_key', 'LIKE', 'plate_type']])->first();

            $get_plate_type = $plate_type->meta_value;

            if (!$car) {
                return response()->json(['error' => 'Car not found'], 404);
            }

            // Attach multiple Images
            $thumbnails = carImages::where('carId', $car->id)->get();
            $carImageUrls = [];
            foreach ($thumbnails as $thumbnail) {
                $image = master_images::whereId($thumbnail->imageId)->first();
                if ($image) { 
                    $carImageUrls[] = [ 'url' => $image->url, 'featured' => $thumbnail->featured ]; 
                }
            }
         
            
            $partnerId = $car->user_id;

            return response()->json(['success' => true, 'error' => false, 'car' => $car, 'plate_type' => $plate_type ? $get_plate_type : null, 
            'modifiedImgUrls' => $carImageUrls
         ], 200);
        }
    }

    public function edit(Request $request,$id)
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

///////////////////////////////////////////////////////////////////////////////////////////////
    public function ajaxShowCars(Request $request)
    {
        $agentId = auth()->user()->id;
     
        $userIds = cars::where('user_id','=',$request->partner_id)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')->pluck('user_id');

        $carIds = cars::whereIn('user_id',$userIds)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')->pluck('id');

        
        $sharedCarId = shareCars::whereIn('car_id',$carIds)->where('agent_id','=',$agentId)->pluck('car_id');


        $cars = cars::whereIn('id', $sharedCarId)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')
        ->get();

        
        // Optimized thumbnail query
        $thumbnails = carImages::whereIn('carId',$cars->pluck('id')->toArray())
        ->where('featured', 'set')
        ->get()
        ->keyBy('carId');
                
        // Attach the feature image URLs to the cars
        $cars = $cars->map(function ($car) use ($thumbnails) {
            $thumbnail = $thumbnails->get($car->id);
            $featuredImage = $thumbnail ? Helper::getPhotoById($thumbnail->imageId) : null;
            $car->featureImageUrl = $featuredImage ? $featuredImage->url : null;
            return $car;
        });
       
        if($cars){
            return response()->json(['success' => true,'error' => false,'cars'=> $cars,'msg'=>'Partner has been retrieved'],200);
        }

        return response()->json(['success' => false,'error' => true,'cars'=> $cars,'msg'=>'Partner has not been retrieved']);

    }





   
}
