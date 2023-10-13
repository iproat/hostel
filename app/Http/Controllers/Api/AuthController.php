<?php

namespace App\Http\Controllers\Api;

use App\User;
use Carbon\Carbon;
use App\Model\Employee;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use App\Model\MsSql;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'migrate', 'sample']]);
    }

    public function sample(Request $request)
    {
        $var = Carbon::now('Asia/Kolkata');
        $time = $var->toTimeString();
        return response()->json([
            'message' => "API works fine",
            'date' => \date('y-m-d H:i:s'),
            'time' => $time,
        ], 200);
    }

    public function login(Request $request)
    {

        $credentials = ['user_name' => $request->email, 'password' => $request->password];

        if ($token = JWTAuth::attempt($credentials)) {

            $userStatus = Auth::user()->status;

            if ($userStatus == UserStatus::$ACTIVE) {

                $employee = Employee::where('user_id', Auth::user()->user_id)->first();

                if (!$employee) {
                    Auth::logout();
                    return response()->json([
                        'status' => false,
                        'message' => 'You are not regisistered as an employee.',
                    ], 200);
                }

                $data = MsSql::where('ID', $employee->finger_id)->whereDate('datetime', Carbon::today());
                $access_log = $data->first();


                return response()->json([
                    'message' => "Login Successful !!!",
                    'status' => true,
                    'access_token' => $token,
                    'is_checked_in' => isset($access_log) ? true : false,
                    'checked_in_data' => $access_log,
                    'employee' => $employee,
                ], 200);
            } elseif ($userStatus == UserStatus::$INACTIVE) {

                Auth::logout();

                return response()->json([
                    'status' => false,
                    'message' => 'You are temporary blocked. please contact to admin',
                ], 200);
            } else {

                Auth::logout();

                return response()->json([
                    'status' => false,
                    'message' => 'You are terminated. please contact to admin',
                ], 200);
            }
        } else {

            return response()->json([
                'status' => false,
                'message' => 'Email or password does not matched',
            ], 200);
        }
    }

    public function logout()
    {
        try {
            auth('api')->logout();
            return Controller::custom_success("User Successfully Logout.");
        } catch (\Throwable $th) {
            return Controller::custom_error('Something went wrong!' . $th->getMessage());
        }
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    protected function createNewToken($token)
    {

        $employee = Employee::where('user_id', auth()->user()->user_id)->first();
        if (!$employee) {
            Auth::logout();
            return response()->json([
                'status' => false,
                'message' => 'You are not regisistered as an employee.',
            ], 200);
        }

        $data = MsSql::where('ID', $employee->finger_id)->whereDate('datetime', Carbon::today());
        $access_log = $data->first();

        return response()->json([
            'message' => "Login Successful !!!",
            'status' => true,
            'access_token' => $token,
            'is_checked_in' => isset($access_log) ? true : false,
            'checked_in_data' => $access_log,
            'employee' => $employee,
        ], 200);
    }
}
