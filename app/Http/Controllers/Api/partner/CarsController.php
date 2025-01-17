<?php
namespace App\Http\Controllers\Api\partner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\cars;
use App\Models\carsMeta;
use Intervention\Image\Facades\Image;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;
use App\Models\master_images;
use App\Models\carImages;
use App\Models\color;
use Helper;
use Session;
use Illuminate\Validation\Rule;

class CarsController extends Controller{

    public function list(Request $request)
    {
        $userId = auth()->user()->id; $countCars = cars::where('status', 'NOT LIKE', 'deleted') ->where('user_id', '=', $userId) ->count();

        $initialCars = cars::where('status', 'NOT LIKE', 'deleted') 
        ->where('user_id', '=', $userId) 
        ->orderBy('created_at', 'desc') 
        ->take(10) ->get();


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
           return response()->json(['success'=>false,'error'=>true, ], 404);
        }
        return response()->json(['success'=>true,'error'=>false, 'cars' => $initialCars, 'totalCount' => $countCars ], 200);
    }


    public function loadMoreCars(Request $request)
    {
        $userId = auth()->user()->id;
        $page = $request->input('page');
        $perPage = $request->input('per_page', 10);
        $initialCarIds = $request->input('initial_car_ids', '');
        $initialCarIds = $initialCarIds !== '' ? explode(',', $initialCarIds) : [];
       
        $cars = cars::where('status', 'NOT LIKE', 'deleted') 
        ->where('user_id', '=', $userId) 
        ->orderBy('created_at', 'desc') 
        ->skip(($page - 1) * $perPage) 
        ->take($perPage) ->get();


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
       
        if(!$cars){
           return response()->json(['success'=>false,'error'=>true, ], 404);
        }
       
        return response()->json(['success'=>true,'error'=>false, 'cars' => $cars, ]);
    }

    public function delete(Request $request, $id){
        if (!$id) {
            return response()->json(['success' => false, 'error' => true, 'msg' => 'Car not deleted'],404);
        }

        cars::whereId($id)->update([
        'status' => 'deleted',
        ]);

        return response()->json(['success' => true, 'error' => false, 'msg' =>'Car deleted'], 200);
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


    public function edit(Request $request,$id)
    {
        if (!$id) {
            return response()->json([ 'success' => false, 'error' => true, ], 400);
        }

        $userId = auth()->user()->id;

        $car = cars::where('user_id', $userId) ->where('status', '!=', 'deleted') ->where('id', $id) ->first();

        $plate_type = carsMeta::where([['carId', '=', $id], ['meta_key', 'LIKE', 'plate_type']])->first();

        $get_plate_type = $plate_type->meta_value;

        $thumbnails = carImages::where('carId', $id)->get();

        $imageObjects = [];

        $featured_check = null;

        foreach ($thumbnails as $thumbnail) {
            $image = master_images::whereId($thumbnail->imageId)->first();
            $featured_check = carImages::where('carId', $id) ->where('featured', 'set') ->first() ?->imageId;
            if ($image) {
                $imageObjects[] = [ 'imageId' => $image->id, 'url' => $image->url, ];
            }
        }

        return response()->json([ 'success' => true, 'error' => false, 'car' => $car, 'plate_type' => $get_plate_type, 'imageObjects' => $imageObjects, 'featured_check' => $featured_check ], 200);

    }

    public function view(Request $request,$id)
    {
        if (!$id) {
            return response()->json([ 'success' => false, 'error' => true, ], 400);
        }

        $userId = auth()->user()->id;

        $car = cars::where('user_id', $userId) ->where('status', '!=', 'deleted') ->where('id', $id) ->first();

        $thumbnails = carImages::where('carId', $car->id)->get();
            $carImageUrls = [];
            foreach ($thumbnails as $thumbnail) {
                $image = master_images::whereId($thumbnail->imageId)->first();
                if ($image) { 
                    $carImageUrls[] = [ 'url' => $image->url, 'featured' => $thumbnail->featured ]; 
                }
            }

        $plate_type = carsMeta::where([['carId', '=', $id], ['meta_key', 'LIKE', 'plate_type']])->first();

        $get_plate_type = $plate_type->meta_value;

        if (!$car) {
            return response()->json(['error' => 'Car not found'], 404);
        }

        return response()->json(['success' => true, 'error' => false, 'car' => $car, 'plate_type' => $plate_type ? $get_plate_type : null, 'modifiedImgUrls' => $carImageUrls,  ], 200);
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
            $numericPart = floatval(preg_replace('/[^0-9.]/', '', $price));
            $trimPriceAmount = round($numericPart, 2);
        } else {
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
                    ->where(fn($query) => $query->whereIn('status', ['available'])),
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
            return response()->json([ 'success' => false, 'error' => true, 'message' => $validator->errors(), ], 400);
        }

        $userId = auth()->user()->id;

        if (isset($request->photoId)) {
            $uploadedPhotosId = $request->photoId;
        }

        $cars = cars::create([
            'name' => strtolower($request->car_name),
            'registration_number' => $request->registration_number,
            'transmission' => $request->transmission_type,
            'fuel_type' => $request->fuel_type,
            'manufacturing_year' => $request->manufacturing_year,
            'car_type' => $request->car_type,
            'roof_type' => $request->roof_type,
            'price' => $trimPriceAmount,
            'user_id' => $userId,
        ]);

        if (!$cars) {
            return response()->json(['success' => false, 'error' => true],404);
        }

        $carId = $cars->id;

        if ($carId) {
            $this->insertOrUpdateCarsmeta($carId, 'plate_type', $request->plate_type);

        if (isset($request->photoId) && !empty($uploadedPhotosId)) {
        
            foreach ($uploadedPhotosId as $photosId) {
              carImages::create(['imageId' => $photosId, 'carId' => $carId, 'status' => 'active', 'featured' => 'not_set']);
            }

            $featuredCarId = $request->featured_check;
            if ($featuredCarId !== null && in_array($featuredCarId, $uploadedPhotosId)) {
                    carImages::where('imageId', $featuredCarId)->update([
                        'imageId' => $featuredCarId,
                        'carId' => $carId,
                        'status' => 'active',
                        'featured' => 'set'
                    ]);
                }
            }
       }

        return response()->json(['success' => true, 'error' => false, 'carId' => $carId], 200);
    }

    public function update(Request $request, $id)
    {
        $price = $request->price;

        if ($price) {
            $numericPart = preg_replace('/[^0-9.]/', '', $price);
            $trimPriceAmount = round($numericPart);
        } else {
            $trimPriceAmount = 0;
        }

        $messages = [
            'car_name.required' => 'car name is required.',
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
            'car_type' => 'required',
            'fuel_type' => 'required',
            'manufacturing_year' => 'required',
            'price' => 'required',
            'transmission_type' => 'required',
            'roof_type' => 'required',
            'plate_type' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([ 'success' => false, 'error' => true, 'message' => $validator->errors(), ], 400);
        }

        $userId = auth()->user()->id;

        if (isset($request->photoId)) {
            $uploadedPhotosId = $request->photoId;
        }

        $cars = cars::whereId($id)->update([
            'name' => strtolower($request->car_name),
            'transmission' => $request->transmission_type,
            'fuel_type' => $request->fuel_type,
            'manufacturing_year' => $request->manufacturing_year,
            'car_type' => $request->car_type,
            'roof_type' => $request->roof_type,
            'price' => $trimPriceAmount,
            'user_id' => $userId,
        ]);

        carImages::where('carId', '=', $id)->delete();

        if ($id) {

            $this->insertOrUpdateCarsmeta($id, 'plate_type', $request->plate_type);

            if (isset($request->photoId) && !empty($uploadedPhotosId)) {

                foreach ($uploadedPhotosId as $photosId) {
                    carImages::create(['imageId' => $photosId, 'carId' => $id, 'status' => 'active', 'featured' => 'not_set']);
                }
                
                $featuredCarId = $request->featured_check;
                if ($featuredCarId !== null) {
                    carImages::where('imageId', $featuredCarId)->update([
                        'imageId' => $featuredCarId,
                        'carId' => $id,
                        'status' => 'active',
                        'featured' => 'set'
                    ]);
                }
            }
        }

        return response()->json([ 'success' => true, 'error' => false, ], 200);
    }


    public function uploadImage(Request $request){
  
        $validator = Validator::make($request->all(), [
            'car_photos.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all(), 'success' => false, 'error' => true]);
        }
        $userId = auth()->user()->id;
        $imageObjects = [];

        foreach ($request->file('car_photos') as $image) {
            if (!$image) {
                return response()->json(['success' => false, 'error' => true, 'message' => 'No image file provided.']);
            }
            $nameWithExtension = time() . '-' . $image->getClientOriginalName();
            $name = pathinfo($nameWithExtension, PATHINFO_FILENAME); 
            $extension = $image->getClientOriginalExtension();

            $destinationPath = public_path('/uploads/CarPhotos/' . $userId . '/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true); 
            }


            $image->move($destinationPath, $name . '.' . $extension);
      
            $originalImage = Image::make($destinationPath . $name . '.' . $extension);

            $sizes = [
                '1920',
                '1024',
                '512',
                '300',
                '512x512',
                '150x150'
            ];

            foreach ($sizes as $size) {
                $resizedImage = clone $originalImage;
                if (strpos($size, 'x') !== false) {
                    list($width, $height) = explode('x', $size);
                    $resizedImage->fit($width, $height); 
                } else {
                    $resizedImage->resize($size, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $resizedImage->save($destinationPath . $name . '-' . $size . '.' . $extension);
            }


            $numericUUID = $this->generateNumericUUID();

            $imageObject = master_images::create([
                'name' => $name,
                'url' => url('/public/uploads/CarPhotos/' . $userId . '/' . $name . '-300.' . $extension),
                'base_url' => $destinationPath . $name . '-300.' . $extension,
                'withoutPublicUrl' => '/uploads/CarPhotos/' . $userId . '/' . $name . '-300.' . $extension,
                'ImageUniqueId' => $numericUUID
            ]);

            $imageObjects[] = [
                'imageId' => $imageObject->id,
                'ImageUniqueId' => $imageObject->ImageUniqueId,
                'url' => $imageObject->url,
            ];
        }

        return response()->json([ 'success' => true, 'error' => false, 'imageObjects' => $imageObjects, ],200);
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
}
