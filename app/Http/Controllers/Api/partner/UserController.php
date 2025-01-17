<?php
namespace App\Http\Controllers\Api\partner;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Drivers;
use Illuminate\Http\Request;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{


    public function getUserListAjax(Request $request)
    {
        $draw = $request->get('draw', 1); // Default draw to 1
        $start = $request->get("start", 0); // Default start to 0
        $rowPerPage = $request->get("length", 10); // Default length to 10
        $orderArray = $request->get('order', []); // Default to an empty array
        $columnNameArray = $request->get('columns', []); // Default to an empty array
        $searchArray = $request->get('search', []); // Default to an empty array

        $columnIndex = $orderArray[0]['column'] ?? 0; // Default to column index 0
        $columnName = $columnNameArray[$columnIndex]['data'] ?? 'id'; // Default to 'id'
        $columnSortOrder = $orderArray[0]['dir'] ?? 'asc'; // Default to ascending order
        $searchValue = $searchArray['value'] ?? ''; // Default to empty string

        $userId = auth()->user()->id;

        // Fetch drivers
        $drivers = Drivers::where('userId', '=', $userId)
            ->orderByDesc('created_at')
            ->get(['id', 'driver_name', 'driver_mobile', 'driver_mobile_country_code', 'created_at']);

        $total = $drivers->count();

        // Sort and slice drivers for pagination
        $drivers = $drivers->sortBy($columnName, SORT_REGULAR, $columnSortOrder === 'desc')
            ->slice($start, $rowPerPage);

        $arrData = collect([]);
        foreach ($drivers as $driver) {
            $arrData[] = [
                'id' => $driver->id,
                'name' => ucwords($driver->driver_name),
                'user_type' => ucwords('driver'),
                'contact' => $driver->driver_mobile_country_code . ' ' . $driver->driver_mobile,
                'action' => [
                    'view_url' => '',
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

           return response()->json(['success'=>true,'error'=>false,'id'=>$driver->id],201);
    }

    public function edit(Request $request,$id){
       
        if(!$id){
            return response()->json(['success'=>false,'error'=>true,],404);
        }

        $driver= Drivers::whereId($id)->first();
        return response()->json(['success'=>true,'error'=>false,'driver'=>$driver],200);
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

        return response()->json(['success'=>true,'error'=>false,'id'=>$id],200);

    }


    public function delete(Request $request ,$id){
        if(!$id){
            return redirect()->route('partner.users.list');
        }
        $driver = Drivers::whereId($id)->delete();
        return response()->json(['success' => true,'error' => false,'msg'=>'User has been deleted'],200);
    }

    public function view(Request $request,$id){
        if(!$id){
            return response()->json(['success' => false,'error' => true,'msg'=>'User not found'],404);
        }
       $driver= Drivers::whereId($id)->first();
       return response()->json(['success' => true,'error' => false, 'driver' => $driver,],200);

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
