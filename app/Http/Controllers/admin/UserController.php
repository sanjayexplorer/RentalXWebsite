<?php
namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMetas;
use App\Models\CarBooking;
use App\Models\Drivers;
use App\Models\cars;
use App\Models\carImages;
use Illuminate\Http\Request;
use Session;
use Auth;
use Hash;
use Helper;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Models\master_images;
class UserController extends Controller
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


    public function index(){
        $users = User::where('id', '!=', auth()->user()->id)
        ->whereIn('status', ['active', 'inactive'])
        ->where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'LIKE', 'partner');
            })
            ->orWhereHas('roles', function ($q) {
                $q->where('name', 'LIKE', 'agent');
            });
        })
        ->with('roles')
        ->orderBy('created_at', 'DESC')
        ->get();
       return view('admin.users.list',compact('users'));
    }

    public function getUserListAjax(Request $request ){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowPerPage= $request->get("length");
        $orderArray = $request->get('order');
        $columnNameArray = $request->get('columns');
        $searchArray = $request->get('search');
        $columnIndex = $orderArray[0]['column'];
        $columnName  = $columnNameArray[$columnIndex]['data'];
        $columnSortOrder = $orderArray[0]['dir'];
        $searchValue = $searchArray['value'];
        $adminId=auth()->user()->id;
        //'orderArray',$columnSortOrder,
        // dd('columnNameArray:',$columnNameArray, 'columnIndex:',$columnIndex,'columnName:',$columnName,'columnSortOrder:',$columnSortOrder);
        $users = User::where('id','!=',auth()->user()->id)
        ->whereIn('status', ['active', 'inactive'])
        ->where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'LIKE', 'partner');
            })
            ->orWhereHas('roles', function ($q) {
                $q->where('name', 'LIKE', 'agent');
            });
        })
        ->with('roles')
        ->orderbydesc('created_at')->get([ 'id','mobile','created_at','status']);

        $total = $users->count();
        $users = $users->sortBy($columnName, SORT_REGULAR, $columnSortOrder)->slice($start, $rowPerPage);

        $arrData = collect([]);
        foreach ($users as $user) {
            $arrData[] = [
                'id' => $user->id,
                'owner_name' => ucwords(Helper::getUserMeta($user->id, 'owner_name')) ?: 'Not Yet Added',
                'user_type'=>strcmp($user->roles[0]['name'], 'partner') == 0 ? 'Rental Company' :
                 ucwords($user->roles[0]['name']),
                'company_name' => ucwords(Helper::getUserMeta($user->id, 'company_name')) ?: 'Not Yet Added',
                'mobile' => $user->mobile ? Helper::getUserMeta($user->id, 'user_mobile_country_code') .'&nbsp;'.$user->mobile : 'Not Yet Added',
                'status' => $user->status ?$user->status: 'Not Yet Added',
                'action' => [
                    'edit_url' => route('admin.users.edit', $user->id),
                    'view_url' => route('admin.users.view', $user->id),
                    'delete_url' => route('admin.users.delete', $user->id),
                    'status_change_url'=>route('admin.users.status.update',$user->id),
                ],
            ];
        }
        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $arrData->toArray(),
        ];
        return response()->json($response);
    }

    public function add(){
        return view('admin.users.add');
    }
    /////// create users
    public function store(Request $request){
        $messages = [
            'owner_name.required' => 'Owner name is required',
            'company_name.required' => 'Company name is required',
            'user_type.required' => 'User type is required',
            'mobile_number.required' => 'Login mobile number is required',
            'mobile_number.unique' => 'mobile number must be unique',
            'mobile_number.max' => 'mobile number must not be greater than 15 digits',
            'company_phone_number.required' => 'Company Phone number is required',
            'password.required' => 'Password is required',
            ];

            $validator = Validator::make($request->all(), [
             'owner_name' => 'required',
               'company_name' => 'required',
               'user_type' => 'required',
               'mobile_number' => [
                'required',
                Rule::unique('users', 'mobile')->where(function ($query) {
                    $query->where('status', 'active')->orWhere('status', 'inactive');
                }),
                'max:15',
            ],
               'company_phone_number' => 'required',
               'password' => 'required',
            ], $messages);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', 'Validation error. Please correct the errors and try again.');
             }

            //  dd($request->all());


            $login_mobile_number = str_replace(' ', '', $request->mobile_number);

             if(strcmp($request->user_type,"agent")==0){
               $user=  User::create([
                    'mobile' => $login_mobile_number,
                    'password'=>Hash::make($request->password),
                ]);

                $user->assignRole('agent');
             }
             elseif(strcmp($request->user_type,"partner")==0){
                $user=User::create([
                    'mobile' => $login_mobile_number,
                    'password'=>Hash::make($request->password),
                ]);

                $driverNames = $request->driver_name;
                if ($driverNames !== null && !empty($driverNames)) {
                   $driverMobiles = $request->driver_mobile;
                   $mobile_number_country_codes = $request->mobile_number_country_code;

                   foreach ($driverNames as $key => $driverName) {
                       if ($driverName !== null && $driverName !== '') {

                           Drivers::create([
                               'userId' => $user->id,
                               'driver_name' => $driverName,
                               'driver_mobile' => $driverMobiles[$key],
                              'driver_mobile_country_code' => $mobile_number_country_codes[$key],

                           ]);
                       }
                   }
                 }
                $user->assignRole('partner');
             }

             $id = $user->id;
             $this->insertOrUpdateUsermeta($id,'company_name', $request->company_name);
             $this->insertOrUpdateUsermeta($id,'CompanyImageId', $request->CompanyImageId);
             $this->insertOrUpdateUsermeta($id,'owner_name', $request->owner_name);
             $this->insertOrUpdateUsermeta($id,'company_phone_number', $request->company_phone_number);
             $this->insertOrUpdateUsermeta($id,'email', $request->email);

             if (strcmp($login_mobile_number, "") != 0) {
                 $this->insertOrUpdateUsermeta($id,'user_mobile_country_code', $request->user_mobile_country_code);
             }
             if (strcmp($request->company_phone_number, "") != 0) {
                $this->insertOrUpdateUsermeta($id,'company_phone_number_country_code', $request->company_phone_number_country_code);
             }

             $this->insertOrUpdateUsermeta($id,'plot_shop_number', $request->plot_shop_number);
             $this->insertOrUpdateUsermeta($id,'street_name', $request->street_name);
             $this->insertOrUpdateUsermeta($id,'zip', $request->zip);
             $this->insertOrUpdateUsermeta($id,'state', $request->state);
             $this->insertOrUpdateUsermeta($id,'city', $request->city);

             Session::flash('success', 'User has been created');
             return redirect()->route('admin.users.view',$id);


    }

    public function edit($id){
        if(!$id)
        {
            return redirect()->route('admin.users.list');
        }
        else{
        $user = User::whereId($id)->whereIn('status', ['active', 'inactive'])->first();
        $drivers= Drivers::where('userId','=',$id)->orderBy('created_at', 'desc')->get();
        return view('admin.users.edit',compact('user','drivers'));
        }
    }

    public function update(Request $request, $id){

        $currentUserMobile = User::find($id)->mobile;
        $messages = [
            'owner_name.required' => 'Owner name is required',
            'company_name.required' => 'Company name is required',
            'user_type.required' => 'User type is required',
            'mobile_number.required' => 'Login mobile number is required',
            'company_phone_number.required' => 'Company Phone number is required',
            ];

            $validator = Validator::make($request->all(), [
               'owner_name' => 'required',
               'company_name' => 'required',
               'user_type' => 'required',
               'mobile_number' => 'required',
               'company_phone_number' => 'required',
            ], $messages);

            $validator->sometimes('mobile_number', 'unique:users,mobile', function ($input) use ($currentUserMobile) {
                return $input->mobile_number != $currentUserMobile;
            });

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', 'Validation error. Please correct the errors and try again.');
             }


            $user = User::where('id', $id)->whereIn('status', ['active', 'inactive'])->first();
            $mobile = str_replace(' ', '', $request->mobile_number);
            $userType = $request->user_type;

            if ($user) {
                $requestPassword =  $request->password;
                if ($requestPassword !== null && $requestPassword !== '') {
                    User::whereId($id)->update([
                        'mobile' => $mobile,
                        'password' => Hash::make($requestPassword),
                    ]);
                } else {
                    User::whereId($id)->update([
                        'mobile' => $mobile,
                    ]);
                }

                $user->syncRoles([$userType]);


                  if(strcmp($userType,"partner")==0){
                    $driverNames = $request->driver_name;
                    if ($driverNames !== null && !empty($driverNames)) {
                        Drivers::where('userId', '=', $id)->delete();
                    $driverMobiles = $request->driver_mobile;
                    $mobile_number_country_codes = $request->mobile_number_country_code;

                    foreach ($driverNames as $key => $driverName) {
                        if ($driverName !== null && $driverName !== '') {

                            Drivers::create([
                                'userId' => $id,
                                'driver_name' => $driverName,
                                'driver_mobile' => $driverMobiles[$key],
                                'driver_mobile_country_code' => $mobile_number_country_codes[$key],

                            ]);
                        }
                    }
                    }
                  }
            }

             $this->insertOrUpdateUsermeta($id,'company_name', $request->company_name);
             $this->insertOrUpdateUsermeta($id,'CompanyImageId', $request->CompanyImageId);
             $this->insertOrUpdateUsermeta($id,'owner_name', $request->owner_name);
             $this->insertOrUpdateUsermeta($id,'company_phone_number', $request->company_phone_number);
             if (strcmp($mobile, "") != 0) {
                $this->insertOrUpdateUsermeta($id,'user_mobile_country_code', $request->user_mobile_country_code);
             }
             if (strcmp($request->company_phone_number, "") != 0) {
                $this->insertOrUpdateUsermeta($id,'company_phone_number_country_code', $request->company_phone_number_country_code);
             }
             $this->insertOrUpdateUsermeta($id,'email', $request->email);
             $this->insertOrUpdateUsermeta($id,'plot_shop_number', $request->plot_shop_number);
             $this->insertOrUpdateUsermeta($id,'street_name', $request->street_name);
             $this->insertOrUpdateUsermeta($id,'zip', $request->zip);
             $this->insertOrUpdateUsermeta($id,'state', $request->state);
             $this->insertOrUpdateUsermeta($id,'city', $request->city);

             Session::flash('success', 'User has been updated');
             return redirect()->route('admin.users.view',$id);

    }

    public function uploadCompanyLogo(Request $request){

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
            catch(NotSupportedException $e)
            {

            }
            catch(ImageException $e)
            {

            }

            catch(InvalidArgumentException $e)
            {
            }
            catch(MissingDependencyException $e)
            {
            }
            catch(NotFoundException $e)
            {
            }
            catch(NotReadableException $e)
            {
            }
            catch(NotWritableException $e)
            {
            }
            catch(RuntimeException $e)
            {
            }
        $image->move(public_path().'/uploads/companyLogo/'.$userId.'/',$name);
        $imageObject=master_images::create(['name'=>$image->getClientOriginalName(),'url'=>$this->getBaseUrlSystem().'/uploads/companyLogo/'.$userId.'/'.$name,'base_url'=>public_path().'/uploads/companyLogo/'.$userId.'/'.$name,'withoutPublicUrl'=>'/uploads/companyLogo/'.$userId.'/'.$name]);
        return response()->json(['success'=>true,'imageId'=>($imageObjectMini)?$imageObjectMini->id:$imageObject->id,'imageUrl'=>$this->getBaseUrlSystem().'/uploads/companyLogo/'.$userId.'/'.$name,'miniImageUrl'=>$miniImageUrl]);
        }
    }

    public function delete(Request $request ,$id){

        if(!$id){
            return redirect()->route('admin.users.list');
        }else{
            User::whereId($id)->delete();
            UserMetas::where('userId', '=', $id)->delete();
            cars::where('user_id','=',$id)->delete();
            $cars = cars::where('user_id','=',$id)->get();
            $carIds = $cars->pluck('id')->toArray();
            carImages::whereIn('carId',$carIds)->delete();
            CarBooking::whereIn('carId', $carIds)->delete();
        return response()->json(['success' => true,'error' => false,'msg'=>'User has been deleted']);
        }
    }

    public function view($id){
        if(!$id){
            return redirect()->route('admin.users.list');
        }else{

            $drivers= Drivers::where('userId','=',$id)->orderBy('created_at', 'desc')->get();
            $user =  User::whereId($id)->whereIn('status', ['active', 'inactive'])->first();
            return view('admin.users.view',compact('user','drivers'));
        }
    }


    public function statusChange(Request $request, $id){
        if(!$id){
            return redirect()->route('admin.users.list');
        }else{
            User::whereId($id)->update([
                'status'=>$request->status,
            ]);

        return response()->json(['success' => true,'error' => false,'msg'=>'status has been updated']);

        }

    }


    public function logout(Request $request)
    {
        Session::flush();
        Auth::logout();
        return redirect()->route('login');
    }

}
