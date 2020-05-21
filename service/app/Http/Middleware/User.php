<?php

namespace App\Http\Middleware;

use Closure;

class User
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        config(['jwt.user' => '\App\Model\Vone\UserModel']);    //重要用于指定特定model
        config(['auth.providers.users.model' => \App\Model\Vone\UserModel::class]);//重要用于指定特定model！！！！
        return $next($request);
    }
}
