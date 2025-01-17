<?php

namespace App\Http\Controllers\Api\partner;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\cars;
use App\Models\carImages;
use App\Models\shareCars;
use App\Models\Drivers;
use App\Models\UserMetas;
use Auth;
use Helper;

use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function list(Request $request)
    {
       
        $userId = Auth()->user()->id;

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

        return response()->json($users, 200);
    }

    public function getAgentListAjax(Request $request)
    {
        // \Log::info('getAgentListAjax request:', $request->all());

        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $orderArray = $request->get('order', []);
        $columnNameArray = $request->get('columns', []);
        $searchArray = $request->get('search', []);

        // Default values
        $columnIndex = isset($orderArray[0]['column']) ? $orderArray[0]['column'] : 0;
        $columnName = isset($columnNameArray[$columnIndex]['data']) ? $columnNameArray[$columnIndex]['data'] : 'id';
        $columnSortOrder = isset($orderArray[0]['dir']) ? $orderArray[0]['dir'] : 'asc';
        $searchValue = isset($searchArray['value']) ? $searchArray['value'] : '';
        $userId=auth()->user()->id;

        // Fetch cars and shared cars
        $cars = cars::where('status', '!=', 'deleted')->where('user_id', $userId)->get();
        $carIds = $cars->pluck('id')->toArray();

        $shared_cars = shareCars::whereIn('car_id', $carIds)->get();
        $agentIds = $shared_cars->pluck('agent_id')->toArray();

        $usersQuery = User::whereIn('id', $agentIds)
            ->whereIn('status', ['active'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'LIKE', 'agent');
            })
            ->with('roles');

        // Apply search filter
        if (!empty($searchValue)) {
            $usersQuery->where(function ($query) use ($searchValue) {
                $query->where('owner_name', 'LIKE', "%$searchValue%")
                      ->orWhere('company_name', 'LIKE', "%$searchValue%")
                      ->orWhere('mobile', 'LIKE', "%$searchValue%");
            });
        }

        // Apply sorting
        $usersQuery->orderBy($columnName, $columnSortOrder);

        // Get total records
        $totalRecords = $usersQuery->count();

        // Paginate the results
        $users = $usersQuery->skip($start)->take($length)->get(['id', 'mobile', 'created_at', 'status']);

        $arrData = collect([]);
        foreach ($users as $user) {
            $arrData->push([
                'id' => $user->id,
                'owner_name' => ucwords(Helper::getUserMeta($user->id, 'owner_name')) ?: 'Not Yet Added',
                'user_type' => $user->roles->first()->name === 'partner' ? 'Rental Company' : ucwords($user->roles->first()->name),
                'company_name' => ucwords(Helper::getUserMeta($user->id, 'company_name')) ?: 'Not Yet Added',
                'mobile' => $user->mobile ? Helper::getUserMeta($user->id, 'user_mobile_country_code') .' '. $user->mobile : 'Not Yet Added',
                'status' => $user->status ?: 'Not Yet Added',
                'action' => [
                    'preview_url' => route('partner.agent.preview', $user->id),
                    'exit_url' => route('partner.agent.exit', $user->id),
                ],
            ]);
        }

        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $arrData->toArray(),
        ];

        return response()->json($response);
    }

    public function agentSearch(Request $request)
    {

        $mobile_number = preg_replace('/\s+/', '', $request->input('mobile_number'));
        $user_mobile_country_code = $request->input('user_mobile_country_code');
        $userId = auth()->id();

        if (empty($mobile_number) && empty($user_mobile_country_code)) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Mobile number and country code are required.'
            ]);
        }

        $agentsByMobileNumber = User::where('id', '!=', $userId)
        ->where('status', '!=', 'inactive')
        ->where('mobile', $mobile_number)
        ->first();

        if (!$agentsByMobileNumber) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'No agent found with the provided mobile number.'
            ]);
        }

        $agentMobileNumber = $agentsByMobileNumber->mobile;
        $userName = Helper::getUserMeta($agentsByMobileNumber->id, 'owner_name');
        $userMobileCountryCode = Helper::getUserMeta($agentsByMobileNumber->id, 'user_mobile_country_code');
        $agentId = $agentsByMobileNumber->id;

        return response()->json([
            'agentMobile' => $agentMobileNumber,
            'userName' => $userName,
            'userMobileCountryCode' => $userMobileCountryCode,
            'agentId'=>$agentId,
            'success' => true,
            'error' => false
        ],200);
    }


    public function selectShareCars(Request $request, $agentId){
        // Get the ID of the currently authenticated user
        $userId = auth()->user()->id;

        // Fetch all cars that are not deleted and belong to the user
        $allcars = cars::where('status', 'NOT LIKE', 'deleted')
            ->where('user_id', '=', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get the car IDs for the user's cars
        $getCarIds = $allcars->pluck('id')->toArray();

        // Get car IDs that have already been shared with the specified agent
        $getSharedCarIds = shareCars::where('agent_id', '=', $agentId)
            ->whereIn('car_id', $getCarIds)
            ->pluck('car_id')
            ->toArray();

        // Filter out the cars that have already been shared with the agent
        $notSharedCarIds = array_diff($getCarIds, $getSharedCarIds);

        // Get the cars that have not been shared with the agent
        $cars = cars::whereIn('id', $notSharedCarIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch thumbnails for the user's cars
        $thumbnails = carImages::whereIn('carId', $getCarIds)
            ->where('featured', 'set')
            ->get();

        // Map the cars to include the feature image URL
        $cars = $cars->map(function ($car) use ($thumbnails) {
            $thumbnail = $thumbnails->where('carId', $car->id)->first();
            $featuredImage = $thumbnail ? Helper::getPhotoById($thumbnail->imageId) : null;
            $car->featureImageUrl = $featuredImage ? $featuredImage->url : null;
            return $car;
        });

        return response()->json([
            'success' => true,
            'error' => false,
            'agentId' => $agentId,
            'userId' => $userId,
            'cars' => $cars,
            // 'user' => $user
        ]);
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

    public function preview(Request $request,$id)
    {

        if(!$id)
        {
            return response()->json(['success'=>false,'error'=>true]);
        }
        else
        {

            $userId = auth()->user()->id;
            $drivers = Drivers::where('userId','=',$id)->get();
            $allcars = cars::where('user_id','=',$userId)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')->get();
            $getCarIds = $allcars->pluck('id');
            $getshareCarIds = shareCars::where('agent_id','=',$id)->whereIn('car_id', $getCarIds)->pluck('car_id');
            $cars = cars::whereIn('id',$getshareCarIds)->orderBy('created_at', 'desc')->get();
            $user =  User::whereId($id)->whereIn('status', ['active', 'inactive'])->first();
            $agentDetail = UserMetas::where('userId', $id)->where('status', 'NOT LIKE', 'inactive')->get();

            // For Single Agent Image
            $imageUrl = '';
            if (strcmp(Helper::getUserMeta($user->id, 'CompanyImageId'), '') != 0) {
                $profileImage = Helper::getPhotoById(Helper::getUserMeta($user->id, 'CompanyImageId'));

                if ($profileImage) {
                $imageUrl = $profileImage->url;
                }
            }
            $modifiedUrl = $imageUrl;

            // Fetch thumbnails for the user's cars
            $thumbnails = carImages::whereIn('carId', $getCarIds)
                ->where('featured', 'set')
                ->get();

            // Map the cars to include the feature image URL
            $cars = $cars->map(function ($car) use ($thumbnails) {
                $thumbnail = $thumbnails->where('carId', $car->id)->first();
                $featuredImage = $thumbnail ? Helper::getPhotoById($thumbnail->imageId) : null;
                $car->featureImageUrl = $featuredImage ? $featuredImage->url : null;
                return $car;
            });

            return response()->json([
                'success'=>true,
                'error'=>false,
                'agentDetail' => $agentDetail,
                'user' => $user,
                'drivers' => $drivers,
                'cars' => $cars,
                'userId' => $userId,
                'modifiedUrl' => $modifiedUrl
            ], 200);

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

    public function unshareCars(Request $request)
    {

        // dd("unshareCars");

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
