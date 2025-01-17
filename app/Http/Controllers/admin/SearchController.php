<?php
namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\UserMetas;
use Illuminate\Support\Facades\DB;
class searchController extends Controller{
    
    
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
    
    public function autocomplete(Request $request)
    {
            $query = $request->input('search');

            $partnersByName = UserMetas::select("meta_value as value", "userId as id", \DB::raw("'PARTNERS' as header, '1' as priority"))
            ->where('meta_key', 'owner_name')
            ->where('status', '!=', 'inactive') 
            ->where('meta_value', 'LIKE', $query . '%')
            ->get();
        
            $partnersPartialByName = UserMetas::select("meta_value as value", "userId as id", \DB::raw("'PARTNERS' as header, '2' as priority"))
            ->where('meta_key', 'owner_name')
            ->where('status', '!=', 'inactive') 
            ->where('meta_value', 'LIKE', '%' . $query . '%')
            ->get();
    

            $results = $partnersByName
            ->merge($partnersPartialByName)
            ->toArray();
            $customComparison = function ($a, $b) use ($query) {
            $similarityA = similar_text(strtolower($query), strtolower($a['value']));
            $similarityB = similar_text(strtolower($query), strtolower($b['value']));
            return $similarityB <=> $similarityA;
            };

            usort($results, $customComparison);
            return response()->json($results);

    }

    public function search(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');
        $header = $request->input('header');
        $query = $request->input('name');
        if($header=='PARTNERS'){
            if(!$id){
            return redirect()->route('admin.users.list');
            }else{
                $user = User::where('id', $id)
                ->where(function ($query) {
                    $query->where('status', '!=', 'inactive')
                          ->orWhere('status', 'active');
                })
                ->first();
                return view('admin.users.view',compact('user'));
            }
        }
        else
        {
            echo "something went wrong";
        }
        
    }
}
?>
