<?php
namespace App\Http\Controllers\Api\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserMetas;
use App\Models\master_images;
use Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        if (Auth::attempt(['mobile' => $request->mobile, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->plainTextToken;
            $role = $user->roles->first()->name;
            $mobile = $user->mobile;
            $UserMetas = UserMetas::where('userId', $user->id)->where('status', 'NOT LIKE', 'inactive')->get();

            $imageUrl = '';
              if (strcmp(Helper::getUserMeta($user->id, 'CompanyImageId'), '') != 0) {
                  $profileImage = Helper::getPhotoById(Helper::getUserMeta($user->id, 'CompanyImageId'));
                  if ($profileImage) {
                  $imageUrl = $profileImage->url;
                  }
              }
            $modifiedUrl = $imageUrl;

            return response()->json([
                'success' => true,
                'data' => [
                    'mobile' => $mobile,
                    'UserMetas' => $UserMetas,
                    'token' => $token,
                    'role' => $role,
                    'modifiedUrl'=>$modifiedUrl
                ],
                'message' => 'User logged in successfully',
            ], 200);

        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        // $request->user()->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }
}
