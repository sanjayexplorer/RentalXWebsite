<?php
namespace App\Http\Controllers\partner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Helper;
use App\Models\leads;
use App\Models\cars;

use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
class LeadsController extends Controller{

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
        $userId=auth()->user()->id;

        $leads = leads::where('user_id','=',$userId)
        ->orderBy('created_at', 'desc')
             ->get();

        return view('partner.leads.list', compact('leads'));

    }

    public function getUserListAjax(Request $request )
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

        $leads = leads::where('user_id','=',$userId)
        ->orderBy('created_at', 'desc')
             ->get();

        $total = $leads->count();
        $leads = $leads->sortBy($columnName, SORT_REGULAR, $columnSortOrder)->slice($start, $rowPerPage);

        $arrData = collect([]);
        foreach ($leads as $lead) {

            $pick_up_date_time = $lead->pick_up_date_time ? date('d F, Y - h:ia', strtotime($lead->pick_up_date_time)) : 'Not Yet Added';

            $arrData[] = [
                'id' => $lead->id,
                'customer_name' => $lead->customer_name ?: 'Not Yet Added',
                'contact_number' => $lead->contact_number ?: 'Not Yet Added',
                'pick_up_date_time' => $pick_up_date_time,
                'pick_up_location' => $lead->pick_up_location ?: 'Not Yet Added',
                'car_model' => $lead->car_model ?: 'Not Yet Added',
                // 'car_type' => $lead->car_type ?: 'Not Yet Added',
                // 'lead_source' => $lead->lead_source ?: 'Not Yet Added',
                'status' => $lead->status ?: 'Not Yet Added',
                'action' => [
                    'call' => $lead->contact_number,
                    // 'message' => route('partner.users.delete', $lead->id),
                    'view_url' => route('partner.leads.view', $lead->id),
                    'status_change_url'=>route('partner.leads.status.update',$lead->id)
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


    public function statusChange(Request $request)
    {

        if(!$request->ids){
            return redirect()->route('partner.users.list');
        }else{

            $status=$request->status;
            leads::whereId($request->ids)->update([
                'status'=>$status,
            ]);

        return response()->json(['success' => true,'error' => false,'msg'=>'status has been updated','status'=>$status]);

        }
    }

    public function add()
    {
        return view('partner.leads.add');
    }

    public function addPost(Request $request)
    {

        $userId = auth()->user()->id;

        $messages = [
            'customer_name.required' => 'customer name is required.',
            'contact_number.unique' => 'contact number must be unique.',

            'pick_up_date_time.required' => 'customer name is required.',
            'pick_up_location.required' => 'pick up location is required.',

            'drop_off_date_time.required' => 'drop off is required.',
            'drop_off_location.required' => 'drop off location is required.',

            'car_model.required' => 'car model is required.',
            'car_type.required'=>'car type is required.',
            'status.required'=>'status is required.',
            'lead_source.required'=>'lead_source is required.'
        ];

        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'contact_number' =>'required ',
            'pick_up_date_time' =>'required ',
            'pick_up_location' => 'required',
            'drop_off_date_time' => 'required',
            'drop_off_location' => 'required',
            'car_model' => 'required',
            'car_type' => 'required',
            'status' => 'required',
            'lead_source' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return redirect()
            ->back()
            ->withInput()
            ->withErrors($validator)
            ->with('error', 'Validation error. Please correct the errors and try again.');
        }


        leads::create([
            'user_id' => $userId,
            'customer_name'=>$request->customer_name,
            'contact_number'=>$request->contact_mobile_country_code.' '.$request->contact_number,

            'pick_up_date_time'=>$request->pick_up_date_time,
            'pick_up_location'=>$request->pick_up_location,

            'drop_off_date_time'=>$request->drop_off_date_time,
            'drop_off_location'=>$request->drop_off_location,

            'car_model'=>$request->car_model,
            'car_type'=>$request->car_type,
            'status'=>$request->status,
            'lead_source'=>$request->lead_source,
        ]);

        return redirect()->route('partner.leads.list')->with('success', 'Car has been added');
    }

    public function view(Request $request ,$id)
    {
       if($id){
            $leads = leads::whereId($id)->first();
            return view('partner.leads.view',compact('leads'));
       }
       else{
        return redirect()->route('partner.leads.list')->with('error', 'invalid id ');
       }
    }

}
?>
