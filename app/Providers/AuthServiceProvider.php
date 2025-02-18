<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->app['auth']->viaRequest('api', function ($request) {
            /*if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }*/
            if ($request->header('Authorization')) {
				$key = explode(' ',$request->header('Authorization'));
				$user = User::where('api_token', $key[1])->first();
				if(!empty($user)){
					$request->request->add(['id' => $user->id]);
				}
				return $user;
			}
        });
    }
}
