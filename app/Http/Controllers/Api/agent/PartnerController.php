<?php

namespace App\Http\Controllers\Api\agent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Drivers;
use App\Models\cars;
use App\Models\shareCars;
use App\Models\carImages;
use Illuminate\Http\Request;
use App\Models\UserMetas;
use Auth;
use Helper;
class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    ////// list page  ////// 
    public function list(Request $request)
    {
        $agentId = auth()->user()->id;

        $get_car_id = shareCars::where('agent_id', '=', $agentId)->pluck('car_id');

        $users = cars::whereIn('id', $get_car_id)
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

        return view('agent.partners.list',compact('users'));
    }

    ////// list page pagination by datatable  ////// 
    public function getPartnerListAjax(Request $request ){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowPerPage= $request->get("length");

        $orderArray = $request->get('order', []); 
        $columnNameArray = $request->get('columns', []);
        $searchArray = $request->get('search', ['value' => '']);

        $columnIndex = isset($orderArray[0]['column']) ? $orderArray[0]['column'] : 0;
        $columnName  = isset($columnNameArray[$columnIndex]['data']) ? $columnNameArray[$columnIndex]['data'] : 'id';
        $columnSortOrder = isset($orderArray[0]['dir']) ? $orderArray[0]['dir'] : 'asc';
        $searchValue = $searchArray['value'] ?? '';


        $agentId = auth()->user()->id;

        $get_car_id = shareCars::where('agent_id', '=', $agentId)->pluck('car_id');

         $partnerIds = cars::whereIn('id', $get_car_id)
        ->where('status', 'NOT LIKE', 'deleted')
        ->orderBy('created_at', 'desc')
        ->pluck("user_id");

        $partners = User::whereIn('id',$partnerIds)
        ->where('id','!=',$agentId)
        ->whereHas(
        'roles', function($q){
            $q->where('name','LIKE', 'partner');
        }
        )
        ->with('roles')
        ->orderbydesc('created_at')->get([ 'id','mobile','created_at','status']);

       $total = $partners->count();
       $partners = $partners->sortBy($columnName, SORT_REGULAR, $columnSortOrder)->slice($start, $rowPerPage);
        $arrData = collect([]);
        foreach ($partners as $partner) {
            $arrData[] = [
                'id' => $partner->id,
                'owner_name' => ucwords(Helper::getUserMeta($partner->id, 'owner_name')) ?: 'Not Yet Added',
                'user_type'=>strcmp($partner->roles[0]['name'], 'partner') == 0 ? 'Rental Company' :
                 ucwords($partner->roles[0]['name']),
                'company_name' => ucwords(Helper::getUserMeta($partner->id, 'company_name')) ?: 'Not Yet Added',
                'mobile' => $partner->mobile ? Helper::getUserMeta($partner->id, 'user_mobile_country_code') .' '.$partner->mobile : 'Not Yet Added',
                'status' => $partner->status ?$partner->status: 'Not Yet Added',
                'action' => [
                    'preview_url' => route('agent.partner.preview', $partner->id),
                    'exit_url' => route('agent.partner.exit', $partner->id),

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


    public function preview(Request $request,$id){
        if(!$id){
            return response()->json(['success' => false,'error' => true]);
        }

        $drivers= Drivers::where('userId','=',$id)->get();
        $user =  User::whereId($id)->whereIn('status', ['active', 'inactive'])->first();

        $partnerDetail = UserMetas::where('userId', $id)->where('status', 'NOT LIKE', 'inactive')->get();

        $imageUrl = '';
        if (strcmp(Helper::getUserMeta($user->id, 'CompanyImageId'), '') != 0) {
            $profileImage = Helper::getPhotoById(Helper::getUserMeta($user->id, 'CompanyImageId'));

            if ($profileImage) {
            $imageUrl = $profileImage->url;
            }
        }
        $modifiedUrl = $imageUrl;

        return response()->json([
            'success'=>true,
            'error'=>false,
            'partnerDetail' => $partnerDetail,
            'user' => $user,
            'drivers' => $drivers,
            'modifiedUrl' => $modifiedUrl
        ], 200);
    }

    public function exit(Request $request){
        
        $agentId = auth()->user()->id;

        $userId = $request->id;

        $shareId = shareCars::where('agent_id','=',$agentId)->get();

        $carIds = $shareId->pluck('car_id');

        $cars = cars::where('status', 'NOT LIKE', 'deleted')
                   ->whereIn('id', $carIds)
                   ->where('user_id','=', $userId)
                   ->orderBy('created_at', 'desc')
                   ->get();

        $carIds = $cars->pluck('id');

        
        if($carIds){
            $deleteShareCar = shareCars::whereIn('car_id',$carIds)->delete();
            return response()->json(['success' => true,'error' => false,'msg'=>'Partnership has been ended']);
        }
        else{
            return response()->json(['success' => false,'error' => true,'msg'=>'Something went wrong']);

        }
        
    }
}