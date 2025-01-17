<?php

namespace App\Http\Controllers\partner;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\cars;
use App\Models\shareCars;
use App\Models\Drivers;
use Illuminate\Http\Request;
use Auth;
use Helper;
class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        if (!Auth::check()) {
            return view('auth.login');
        }
        $userId = auth()->user()->id;
        $user = User::whereId($userId)->where('status', '!=', 'inactive')->with('roles')->first();
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

    ////// list page  ////// 
    public function list(){

        $userId = auth()->user()->id;

        $cars = cars::where('status', '!=', 'deleted')->where('user_id', $userId)->get();
        $carIds = $cars->pluck('id')->toArray();

        $shared_cars = shareCars::whereIn('car_id', $carIds)->get();

        $agentIds = [];
        
        foreach ($shared_cars as $value) {
            $agentIds[] = $value->agent_id; 
        }
        
        $users = User::whereIn('id', $agentIds)
        ->whereIn('status', ['active'])
        ->whereHas('roles', function ($query) {
            $query->where('name', 'LIKE', 'agent');
        })
        ->with('roles')
        ->orderBy('created_at', 'DESC')
        ->get();

        return view('partner.agents.list',compact('users'));
    }

    public function getAgentListAjax(Request $request ){
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
        $userId = auth()->user()->id;

        $cars = cars::where('status', '!=', 'deleted')->where('user_id', $userId)->get();
        $carIds = $cars->pluck('id')->toArray();

        $shared_cars = shareCars::whereIn('car_id', $carIds)->get();

        $agentIds = [];

        foreach ($shared_cars as $value) {
            $agentIds[] = $value->agent_id; 
        }


        $users = User::whereIn('id', $agentIds)
        ->whereIn('status', ['active'])
        ->whereHas('roles', function ($query) {
            $query->where('name', 'LIKE', 'agent');
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
                    'preview_url' => route('partner.agent.preview', $user->id),
                    'exit_url' => route('partner.agent.exit', $user->id),

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


    ////// agent add  ////// 
    public function add(){
        return view('partner.agents.add');
    }

    public function agentSearch(Request $request){
     
        $query = $request->agent_search;
        if(strcmp($query,'')!=0){
            $agentsByMobileNumber = User::where('id', '!=', auth()->user()->id)
            ->whereIn('status', ['active'])
            ->where(function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'LIKE', 'agent');
                });
            })
            ->with('roles')
            ->orderBy('created_at', 'DESC')
            ->where('mobile', 'LIKE', '%' . $query . '%')
            ->first();
            
            if($agentsByMobileNumber)
            {
                $userName = Helper::getUserMeta($agentsByMobileNumber->id, 'owner_name');
                $userMobileCountryCode = Helper::getUserMeta($agentsByMobileNumber->id, 'user_mobile_country_code');

                return response()->json(['agents'=>$agentsByMobileNumber,'userName'=>$userName,'userMobileCountryCode'=>$userMobileCountryCode,'success'=>true,'error'=>false]);
            }
            else{
                return response()->json(['success'=>false,'error'=>true]);
            }
        }
        
    }

    public function selectShareCars(Request $request, $agentId){

        $userId = auth()->user()->id;

        $cars = cars::where('status','NOT LIKE','deleted')->where('user_id','=',$userId)->orderBy('created_at', 'desc')->get();

        return view('partner.agents.view',compact('agentId','userId','cars'));
       
    }

    public function shareCars(Request $request){
      
         $carIds = $request->carIds;
         $agentId = $request->agentId;
        
         $cars = cars::where('status', 'NOT LIKE', 'deleted')->whereIn('id', $carIds)->orderBy('created_at', 'desc')->get();
        
         $agent = User::findOrFail($agentId);
        
         foreach ($cars as $car) {
            $agent->cars()->attach($car->id);
         }
        
         $sharedCars = $agent->cars()->get();

         if($sharedCars){
            return response()->json(['success'=>true,'error'=>false]);
         }
         else {
             return response()->json(['success'=>false,'error'=>true]);
        
         }

    }

    public function preview($id){
        if(!$id){

            return redirect()->route('partner.agent.list');

        }
        else{

            $userId = auth()->user()->id;

            $drivers = Drivers::where('userId','=',$id)->get();

            $allcars = cars::where('user_id','=',$userId)->orderBy('created_at', 'desc')->get();

             $getCarIds = $allcars->pluck('id');

             $getshareCarIds = shareCars::where('agent_id','=',$id)->whereIn('car_id', $getCarIds)->pluck('car_id');

             $cars = cars::whereIn('id',$getshareCarIds)->orderBy('created_at', 'desc')->get();

            $user =  User::whereId($id)->whereIn('status', ['active', 'inactive'])->first();

            return view('partner.agents.preview',compact('user','drivers','cars','userId'));
        }
    }
    public function exit(Request $request){
    
        $userId = auth()->user()->id;

        $agentId = $request->ids;

        $shareId = shareCars::where('agent_id','=',$agentId)->first();

        $carIds = $shareId->pluck('car_id');

        $cars = cars::where('status', 'NOT LIKE', 'deleted')
                   ->whereIn('id', $carIds)
                   ->where('user_id','=', $userId)
                   ->orderBy('created_at', 'desc')
                   ->get();

        $carIds = $cars->pluck('id');


        if($carIds){
            $shareId = shareCars::whereIn('car_id',$carIds)->delete();
            return response()->json(['success' => true,'error' => false,'msg'=>'Partnership has been ended']);
        }else{
            return response()->json(['success' => false,'error' => true,'msg'=>'Something went wrong']);

        }

    }

    public function unshareCars(Request $request){
        $carId = $request->carId;
        $carIds = $request->carIds;
        $agentId = $request->agentId;
        if(strcmp($request->type,'singleDelete') == 0){
            $shareId = shareCars::where('agent_id','=',$agentId)->where('car_id','=',$carId)->delete();
            return response()->json(['success' => true,'error' => false,'msg'=>'Cars has been unshared']);
        }
        if(strcmp($request->type,'multipleDelete') == 0){
            $shareIds = shareCars::where('agent_id','=',$agentId)->whereIn('car_id', $carIds)->delete();
            return response()->json(['success' => true,'error' => false,'msg'=>'Cars has been unshared']);
        }
        if(strcmp($carId,'') == 0 || strcmp($carIds,'') == 0){
            return response()->json(['success' => false,'error' => true,'msg'=>'Something went wrong']);
        }
    }
   
}