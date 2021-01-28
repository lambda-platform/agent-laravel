<?php

namespace Lambda\Agent\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Config;
use Lambda\Agent\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
//        $this->middleware('jwt', ['except' => ['login', 'asyncLogin']]);
    }

    public function login()
    {
        //Returning login page
        if (request()->isMethod('get')) {
            return view('agent::login');
        }

        //Validating
        $credentials = request()->only('login', 'password');
        $validator = Validator::make($credentials, [
            'login' => 'required|string|max:255',
            'password' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()]);
        }

        //JWT Auth
        if (request()->ajax() || request()->wantsJson()) {
            return $this->jwtLogin($credentials, 'form');
        }
    }

    public function loginfb()
    {
        //Returning login page
        if (request()->isMethod('get')) {
            return view('agent::login');
        }

        //Validating
        $credentials = request()->only('userid');
        $validator = Validator::make($credentials, [
            'userid' => 'required|string|max:255',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()]);
        }

        //JWT Auth
        if (request()->ajax() || request()->wantsJson()) {
            return $this->jwtLogin($credentials, 'fb');
        }
    }

    public function postLogin($credentials)
    {
        if (Auth::attempt($credentials)) {
            return response()->json(['status' => true], 200);
        } else {
            dd('I am here not');
            //  return redirect()->back()->withErrors(['error' => 'Нэвтрэх нэр эсвэл нууц үг буруу байна.'])->withInput();
        }
    }

    public function jwtLogin($credentials, $logintype)
    {
        try {
            if ($logintype == 'form') {
                $token = JWTAuth::attempt($credentials, ['exp' => Carbon::now()->addWeek()->timestamp]);
            } else if ($logintype == 'fb') {
                $user = User::where('fb_id', $credentials['userid'])->first();
                $token = JWTAuth::fromUser($user, ['exp' => Carbon::now()->addWeek()->timestamp]);
            }
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'error' => 'Could not authenticate', 'exception' => $e->getMessage()], 500);
        }

        if (!$token) {
            return response()->json(['status' => false, 'error' => 'Unauthorized'], 401);
        } else {
            $meta = $this->respondWithToken($token);

            return response()
                ->json([
                    'status' => true,
                    'data' => request()->user(),
                    'meta' => $meta,
                    'path' => $logintype == 'fb'?$user->role:$this->checkRole(auth()->user()->role),
                ], 200)
                ->withCookie(cookie('token', $token, auth()->factory()->getTTL() * 86400));
        }
    }

    public function checkRole($role)
    {
        $config = Config::get('lambda');
        $roleRedirects = $config['role-redirects'];
//        $defaultRedirect = '/' . env('LAMBDA_APP_NAME', 'mle');
        $defaultRedirect = $config['app_url'];

        foreach ($roleRedirects as $roleRedirect) {
            if ($roleRedirect['role_id'] == $role) {
                return $roleRedirect['url'];
            }
        }

        if ($role != 1) {
            //quiz custom
            $user_group = DB::table('roles')->where('id', $role)->first();

            if ($user_group) {
                if ($user_group->permissions) {
                    $permissions = json_decode($user_group->permissions);
                    if ($permissions->default_menu) {
                        return $defaultRedirect . '/#/p/' . $permissions->default_menu;
                    } else {
                        return response()->json(['status' => false, 'error' => 'Unauthorized'], 401);
                    }

                } else {
                    return response()->json(['status' => false, 'error' => 'Unauthorized'], 401);
                }
            }
        }

        return $defaultRedirect;
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->to('auth/login');
        // return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Agent',
            'expires_in' => auth()->factory()->getTTL() * 86400,
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
