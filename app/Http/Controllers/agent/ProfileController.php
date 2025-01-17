<?php
namespace App\Http\Controllers\agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\User;
use App\Models\master_images;
use App\Models\Drivers;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
class ProfileController extends Controller{

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

    public function profile(){
       $userId = Auth::user()->id;
       $user = User::where('status', 'NOT LIKE', 'inactive')->where('id','=',$userId)->first();
       return view('agent.settings.viewprofile',compact('userId','user'));
    }
    public function EditProfile(){
       $userId = Auth::user()->id;
       $user = User::where('status', 'NOT LIKE', 'inactive')->where('id','=',$userId)->first();
       return view('agent.settings.editprofile',compact('userId','user'));
    }

    public function ProfileUpdate(Request $request ,$id){

        $userId = Auth::user()->id;

        $currentUserMobile = User::where('status', 'NOT LIKE', 'inactive')->find($id)->mobile;

        $messages = [
            'company_name.required' => 'Company name is required',
            'owner_name.required' => 'Owner name is required',
            'login_mobile_number.required' => 'Login mobile number is required',
            'agent_short_name.required' => 'Short name is required',
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
           return redirect()
               ->back()
               ->withInput()
               ->withErrors($validator)
               ->with('error', 'Validation error. Please correct the errors and try again.');
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

      return redirect()->route('agent.profile')->with('success','Profile has been updated');
     }

      public function ProfilePhotoUpdate(Request $request){
        $userId = auth()->user()->id;
        $image=$request->photo;
        $name=$image->getClientOriginalName();
        $mimeType=$image->getMimeType();
        $name=time().$name;
        $validator = Validator::make($request->all(), [
            'photo' => 'required|mimes:jpeg,jpg,png,webp,PNG,JPG,JPEG,WEBP|max:10240'
       ]);
       if ($validator->fails())
       {
          return response()->json(['errors'=>$validator->errors()->all(),'success'=>false,'error'=>true]);
       }
       else
       {
        if(!file_exists(public_path().'/uploads/'))
        {
            mkdir(public_path().'/uploads/');
        }
        if(!file_exists(public_path().'/uploads/companyLogo'))
        {
            mkdir(public_path().'/uploads/companyLogo');
        }
        if(!file_exists(public_path().'/uploads/companyLogo/'.$userId))
        {
            mkdir(public_path().'/uploads/companyLogo/'.$userId);
        }
        if (!file_exists(public_path().'/uploads/companyLogo/'.$userId.'/172x172/')) {

            mkdir(public_path().'/uploads/companyLogo/'.$userId.'/172x172/');

        }
        $miniImageUrl = $this->getBaseUrlSystem().'/uploads/companyLogo/'.$userId.'/'.$name;
        $imageObjectMini = false;
        try{
            $image=$request->file('photo');
            $image_resize = Image::make($image->getRealPath());
            $image_resize->save(public_path().'/uploads/companyLogo/'.$userId.'/172x172/'.$name,100);
            $miniImageUrl = $this->getBaseUrlSystem().'/uploads/companyLogo/'.$userId.'/172x172/'.$name;
            $imageObjectMini=master_images::create(['name'=>$image->getClientOriginalName(),'url'=>$this->getBaseUrlSystem().'/uploads/companyLogo/'.$userId.'/172x172/'.$name,'base_url'=>public_path().'/uploads/companyLogo/'.$userId.'/172x172/'.$name,'withoutPublicUrl'=>'/uploads/companyLogo/'.$userId.'/172x172/'.$name]);
        }
        catch(\Exception $e){
        }
        catch(NotSupportedException $e){
        }
        catch(ImageException $e){
        }
       catch(InvalidArgumentException $e){
      }
      catch(MissingDependencyException $e){
      }
      catch(NotFoundException $e){
      }
      catch(NotReadableException $e){
      }
      catch(NotWritableException $e){
      }
      catch(RuntimeException $e){
      }
      $image->move(public_path().'/uploads/companyLogo/'.$userId.'/',$name);
      $imageObject=master_images::create(['name'=>$image->getClientOriginalName(),'url'=>$this->getBaseUrlSystem().'/uploads/companyLogo/'.$userId.'/'.$name,'base_url'=>public_path().'/uploads/companyLogo/'.$userId.'/'.$name,'withoutPublicUrl'=>'/uploads/companyLogo/'.$userId.'/'.$name]);
      return response()->json(['success'=>true,'imageId'=>($imageObjectMini)?$imageObjectMini->id:$imageObject->id,'imageUrl'=>$this->getBaseUrlSystem().'/uploads/companyLogo/'.$userId.'/'.$name,'miniImageUrl'=>$miniImageUrl]);
     }
  }

  public function ProfilePhotoRemove(Request $request){
    if(!$request->userId){
        return redirect()->route('agent.settings.editprofile');
    }else{
        $this->insertOrUpdateUsermeta($request->userId,'CompanyImageId', 0);
        return response()->json(['success'=>true,'error'=>false,'msg'=>'Profile has been removed.']);
    }
  }
}
