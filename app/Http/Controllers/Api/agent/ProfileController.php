<?php
namespace App\Http\Controllers\Api\agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\User;
use App\Models\UserMetas;
use App\Models\master_images;
use App\Models\Drivers;
use Helper;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
class ProfileController extends Controller{

    public function profile(Request $request){
        $userId = Auth::user()->id; 
        $companyImageId = null;
        $companyImageUrl = null;

        $user = User::where('id', $userId)
                    ->where('status', 'NOT LIKE', 'inactive')
                    ->first();


        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => true,
                'msg' => 'User not found or inactive.'
            ], 404);
        }

        $UserMetas = UserMetas::where('userId', $userId)
                            ->where('status', 'NOT LIKE', 'inactive')
                            ->get();

        $companyImageMeta = UserMetas::where('userId', $userId)
                                    ->where('meta_key', 'CompanyImageId')
                                    ->where('status', 'active')
                                    ->first();

        if ($companyImageMeta) {
            $companyImageId = $companyImageMeta->meta_value;

            $photoDetails = Helper::getPhotoById($companyImageId);
            if ($photoDetails) {
                $companyImageUrl = $photoDetails->url ?? null;
            }
        }

        return response()->json([
            'success' => true,
            'error' => false,
            'user' => $user,
            'userMetas' => $UserMetas,
            'companyImageId' => $companyImageId,
            'companyImageUrl' => $companyImageUrl,
            'msg' => 'Profile has been successfully fetched.'
        ],200);
    }

    public function EditProfile(Request $request){
        $userId = Auth::user()->id; 
        $companyImageId = null;
        $companyImageUrl = null;

        $user = User::where('id', $userId)
                    ->where('status', 'NOT LIKE', 'inactive')
                    ->first();


        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => true,
                'msg' => 'User not found or inactive.'
            ], 404);
        }

        $UserMetas = UserMetas::where('userId', $userId)
                            ->where('status', 'NOT LIKE', 'inactive')
                            ->get();

        $companyImageMeta = UserMetas::where('userId', $userId)
                                    ->where('meta_key', 'CompanyImageId')
                                    ->where('status', 'active')
                                    ->first();

        if ($companyImageMeta) {
            $companyImageId = $companyImageMeta->meta_value;

            $photoDetails = Helper::getPhotoById($companyImageId);
            if ($photoDetails) {
                $companyImageUrl = $photoDetails->url ?? null;
            }
        }


        return response()->json([
            'success' => true,
            'error' => false,
            'user' => $user,
            'userMetas' => $UserMetas,
            'companyImageId' => $companyImageId,
            'companyImageUrl' => $companyImageUrl,
            'msg' => 'Profile has been successfully fetched.'
        ],200);
    }

    public function ProfileUpdate(Request $request ,$id){
       
        $userId = Auth::user()->id;

        $currentUserMobile = User::where('status', 'NOT LIKE', 'inactive')->find($id)->mobile;

        $messages = [
            'company_name.required' => 'Company name is required',
            'owner_name.required' => 'Owner name is required',
            'login_mobile_number.required' => 'Login mobile number is required',
            'agent_short_name.required' => 'Agent short name is required',
            'company_phone_number.required' => 'Company phone number is required',
         ];
        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
            'owner_name' => 'required',
            'login_mobile_number' => 'required',
            'agent_short_name' => 'required',
            'company_phone_number' => 'required',

        ], $messages);

        $validator->sometimes('login_mobile_number', 'unique:users,mobile', function ($input) use ($currentUserMobile) {
            return $input->login_mobile_number != $currentUserMobile;
        });

       if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'error' => true,
            'withErrors' => $validator
        ]);
        }
        
        $user = User::where('id', $id)->where('status', 'NOT LIKE', 'inactive')->first();

        $login_mobile_number = str_replace(' ','',$request->login_mobile_number);

        if ($user) {
            $requestPassword =  $request->password;
            if ($requestPassword !== null && $requestPassword !== '') {
            User::whereId($id)->update([
                'mobile' => $login_mobile_number,
                'password' => Hash::make($requestPassword),
            ]);
          } else {
            User::whereId($id)->update([
                'mobile' => $login_mobile_number,
            ]);
          }
        }

        $this->insertOrUpdateUsermeta($id,'CompanyImageId', $request->CompanyImageId);
        $this->insertOrUpdateUsermeta($id,'company_phone_number', $request->company_phone_number);
        $this->insertOrUpdateUsermeta($id,'company_name', $request->company_name);
        $this->insertOrUpdateUsermeta($id,'email', $request->email);
        $this->insertOrUpdateUsermeta($id,'owner_name', $request->owner_name);
        $this->insertOrUpdateUsermeta($id,'street_name', $request->street_name);


        if (strcmp($login_mobile_number, "") != 0) {
            $this->insertOrUpdateUsermeta($id,'user_mobile_country_code', $request->user_mobile_country_code);
        }

        if (strcmp($request->company_phone_number, "") != 0) {
            $this->insertOrUpdateUsermeta($id,'company_phone_number_country_code', $request->company_phone_number_country_code);
        }
    
        $this->insertOrUpdateUsermeta($id,'plot_shop_number', $request->plot_shop_number);
        $this->insertOrUpdateUsermeta($id,'zip', $request->zip);
        $this->insertOrUpdateUsermeta($id,'state', $request->state);
        $this->insertOrUpdateUsermeta($id,'city', $request->city);
        $this->insertOrUpdateUsermeta($id,'agent_short_name', $request->agent_short_name);
        
        return response()->json([
            'success' => true,
            'error' => false,
            'msg' => 'Profile has been updated'
        ]);
    }

    public function ProfilePhotoUpdate(Request $request,$id){

        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all(), 'success' => false, 'error' => true]);
        }

        $userId = auth()->user()->id;
        $image = $request->file('photo');

        if (!$image) {
            return response()->json(['success' => false, 'error' => true, 'message' => 'No image file provided.']);
        }

        $nameWithExtension = time() . '-' . $image->getClientOriginalName();
        $name = pathinfo($nameWithExtension, PATHINFO_FILENAME); // Get the name without extension
        $extension = $image->getClientOriginalExtension(); // Get the original extension

        // Define the destination path
        $destinationPath = public_path('/uploads/companyLogo/' . $userId . '/');

        // Check if the directory exists, if not, create it
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true); // Create the directory if it doesn't exist
        }

        // Move the file to the specified directory
        $image->move($destinationPath, $name . '.' . $extension);

        // Process the image (resize to only 172x172)
        $originalImage = Image::make($destinationPath . $name . '.' . $extension);

        // Resize the image to 172x172
        $resizedImage = $originalImage->fit(172, 172); // Hard crop to fixed size

        // Save the resized image
        $resizedImage->save($destinationPath . $name . '-172x172.' . $extension);

        // Create a new image record in the database
        $numericUUID = $this->generateNumericUUID();

        $imageObject = master_images::create([
            'name' => $name,
            'url' => url('/public/uploads/companyLogo/' . $userId . '/' . $name . '-172x172.' . $extension),
            'base_url' => $destinationPath . $name . '-172x172.' . $extension,
            'withoutPublicUrl' => '/uploads/companyLogo/' . $userId . '/' . $name . '-172x172.' . $extension,
            'ImageUniqueId' => $numericUUID
        ]);

        return response()->json([
            'success' => true,
            'error' => false,
            'imageObject' => [
                'imageId' => $imageObject->id,
                'ImageUniqueId' => $imageObject->ImageUniqueId,
                'url' => $imageObject->url,
            ],
        ], 200);
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

  public function ProfilePhotoRemove(Request $request){
    if(!$request->userId){
        return response()->json(['success'=>false,'error'=>true, 'msg'=>'Something went wrong.']);
    }
    $this->insertOrUpdateUsermeta($request->userId,'CompanyImageId', 0);
    return response()->json(['success'=>true,'error'=>false,'msg'=>'Profile has been removed.']);
    
  }
}