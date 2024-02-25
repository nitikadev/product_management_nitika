<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Guard string. // web, api
     * 
     * @var string
     */
    protected $guard = 'web';

    /**
     * Authentication driver.
     *
     * @var \Illuminate\Auth\SessionGuard|\Tymon\JWTAuth\JWTGuard
     */
    protected $auth;

    public function __construct()
    {
        $this->auth = $this->authManager();
    }

    /**
     * Authentication driver.
     *
     * @return \Illuminate\Auth\SessionGuard|\Tymon\JWTAuth\JWTGuard
     */
    protected function authManager()
    {
        return auth($this->guard);
    }

    /**
     * Return current logged in user.
     *
     * @return \App\Models\User|null
     */
    public function user()
    {
        if (!$this->auth) {
            $this->auth = $this->authManager();
        }

        return $this->auth->user();
    }

    public function isRequestForWeb()
    {
        return $this->guard === 'web';
    }

    protected function validation_error_response(Validator $validator)
    {
        $_errors = $validator->errors()->messages();
        
        return response()->json([
            'status' => false,
            'data' => [
                'error' => $_errors
            ]
        ]);
    }
}
