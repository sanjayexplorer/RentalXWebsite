<?php
namespace App\Http\Controllers\partner;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Drivers;
use Illuminate\Http\Request;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
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


    public function index()
    {
        $userId= auth()->user()->id;
        $drivers = Drivers::where('userId','=',$userId)->orderBy('created_at', 'DESC')->get();
        return view('partner.users.list',compact('drivers'));
    }

    public function getUserListAjax(Request $request)
    { 
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
        $userId=auth()->user()->id;

        $drivers = Drivers::where('userId','=',$userId)->orderbydesc('created_at')->get([ 'id','driver_name','driver_mobile','driver_mobile_country_code','created_at']);

        $total = $drivers->count();

        $drivers = $drivers->sortBy($columnName, SORT_REGULAR, $columnSortOrder)->slice($start, $rowPerPage);

        $arrData = collect([]);

        foreach ($drivers as $driver) {
            $arrData[] = [
                'id' => $driver->id,
                'name' => ucwords($driver->driver_name),
                'user_type'=>ucwords('driver'),
                'contact' => $driver->driver_mobile_country_code .' '.$driver->driver_mobile,
                'action' => [
                'edit_url'   => route('partner.users.edit',   $driver->id),
                'view_url'   => route('partner.users.view',   $driver->id),
                'delete_url' => route('partner.users.delete', $driver->id),
                'status_change_url' => 'javascript:void(0);',
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

    public function add()
    {
        return view('partner.users.add');
    }

    /////// create users
    public function store(Request $request)
    {

        $messages = [
            'name.required' => 'Name is required',
            'user_type.required' => 'User type is required',
            'mobile_number.required' => 'Mobile number is required',
            ];

            $validator = Validator::make($request->all(), [
               'name' => 'required',
               'user_type' => 'required',
               'mobile_number' => 'required',
            ], $messages);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', 'Validation error. Please correct the errors and try again.');
             }

            $userId = Auth::user()->id;

            $driver= Drivers::create([
            'userId' => $userId,
            'driver_name' => $request->name,
            'driver_mobile' => $request->mobile_number,
            'driver_mobile_country_code' => $request->mobile_number_country_code,
            ]);

            Session::flash('success', 'User has been created');
            return redirect()->route('partner.users.view',$driver->id);
    }

    public function edit($id)
    {
        if(!$id)
        {
            return redirect()->route('partner.users.list');
        }
        else
        {
            $driver= Drivers::whereId($id)->first();
            return view('partner.users.edit',compact('driver'));
        }
    }

    public function update(Request $request, $id)
    {

        $messages = [
            'name.required' => 'Name is required',
            'user_type.required' => 'User type is required',
            'mobile_number.required' => 'Mobile number is required',
            'mobile_number_country_code.required'=>'Mobile number country code is required',
            ];

            $validator = Validator::make($request->all(), [
               'name' => 'required',
               'user_type' => 'required',
               'mobile_number' => 'required',
            ], $messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Validation error. Please correct the errors and try again.');
            }

        $userId = Auth::user()->id;
        $driver = Drivers::whereId($id)->first();
        $driver_mobile_country_code = '';
        if(strcmp($request->mobile_number_country_code,'')==0){
            $driver_mobile_country_code = $driver->driver_mobile_country_code;
        }else{
            $driver_mobile_country_code = $request->mobile_number_country_code;
        }

        Drivers::whereId($id)->update([
            'userId' => $userId,
            'driver_name' => $request->name,
            'driver_mobile' => $request->mobile_number,
            'driver_mobile_country_code' => $driver_mobile_country_code,
        ]);

        Session::flash('success', 'User has been updated');
        return redirect()->route('partner.users.view',$id);

    }

 
    public function delete(Request $request ,$id){
        if(!$id){
            return redirect()->route('partner.users.list');
        }else{
        $driver = Drivers::whereId($id)->delete();
        return response()->json(['success' => true,'error' => false,'msg'=>'User has been deleted']);
        }
    }

    public function view($id){
        if(!$id){
            return redirect()->route('partner.users.list');
        }else{
            $driver= Drivers::whereId($id)->first();
            return view('partner.users.view',compact('driver'));
        }
    }


    public function statusChange(Request $request, $id){
        if(!$id){
            return redirect()->route('partner.users.list');
        }else{
            Drivers::whereId($id)->update([
                'status'=>$request->status,
            ]);
        return response()->json(['success' => true,'error' => false,'msg'=>'status has been updated']);

        }

    }

}
