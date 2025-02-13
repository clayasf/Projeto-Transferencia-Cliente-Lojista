<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Usuario;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\Providers\JWTAuthGuard;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    // public function boot()
    // {
    //     // Here you may define how you wish users to be authenticated for your Lumen
    //     // application. The callback which receives the incoming request instance
    //     // should return either a User instance or null. You're free to obtain
    //     // the User instance via an API token or any other method necessary.

    //     $this->app['auth']->viaRequest('api', function ($request) {
    //       /*  if ($request->input('api_token')) {
    //             return User::where('api_token', $request->input('api_token'))->first();
    //         }
    //         */
            
    //         return Usuario::where('email' , $request->input('email'))->first();
    //     });

    // }
    
    protected function registerGuards()
    {
        $this->app['auth']->extend('jwt', function ($app, $name, array $config) {
            return new JWTAuthGuard($app['request'], $app['auth']->createUserProvider($config['provider']));
        });
    }

}


