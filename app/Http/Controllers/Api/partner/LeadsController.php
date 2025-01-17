<?php

namespace App\Http\Controllers\Api\partner;
use App\Http\Controllers\Controller;
use App\Models\leads;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadsController extends Controller
{
    public function index(Request $request){
      
        $userId=auth()->user()->id;

        $leads = leads::where('user_id','=',$userId)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($leads, 200); 

    }
    


    public function getLeadsListAjax(Request $request){

     try {
        // \Log::info('Request Data:', $request->all());
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

        $userId = auth()->user()->id;
        $query = leads::where('user_id', $userId);
        

        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('customer_name', 'like', "%{$searchValue}%")
                ->orWhere('contact_number', 'like', "%{$searchValue}%")
                ->orWhere('pick_up_location', 'like', "%{$searchValue}%")
                ->orWhere('car_model', 'like', "%{$searchValue}%");
            });
        }

        $total = $query->count();
        $leads = $query->orderBy($columnName, $columnSortOrder)
                    ->skip($start)
                    ->take($length)
                    ->get();


        $arrData = $leads->map(function($lead) {
            return [
                'id' => $lead->id,
                'customer_name' => $lead->customer_name ?: 'Not Yet Added',
                'contact_number' => $lead->contact_number ?: 'Not Yet Added',
                'pick_up_date_time' => $lead->pick_up_date_time ? date('d F, Y - h:ia', strtotime($lead->pick_up_date_time)) : 'Not Yet Added',
                'pick_up_location' => $lead->pick_up_location ?: 'Not Yet Added',
                'car_model' => $lead->car_model ?: 'Not Yet Added',
                'status' => $lead->status ?: 'Not Yet Added',
                'action' => [
                    'call' => $lead->contact_number,
                    'view_url' => '',
                    'status_change_url' => ''
                ],
            ];
        });
        
        return response()->json([
            "draw" => intval($draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $arrData,
        ],200);

     } catch (\Exception $e) {
        // \Log::error('Error in getUserListAjax: ' . $e->getMessage());
        return response()->json(['error' => 'Server Error'], 500);
    }
}


    public function addPost(Request $request){
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
           'contact_number'=>'+91'.' '.$request->contact_number,

           'pick_up_date_time'=>'2005-05-06 06:51:00',
           'pick_up_location'=>$request->pick_up_location,

           'drop_off_date_time'=>'2005-05-06 06:51:00',
           'drop_off_location'=>$request->drop_off_location,

           'car_model'=>$request->car_model,
           'car_type'=>$request->car_type,
           'status'=>$request->status,
           'lead_source'=>$request->lead_source,
           'notes' => $request->notes,
       ]);
        return response()->json(['success' => true,'data'=> $request->all()], 200);
    }
}
