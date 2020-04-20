<?php

return [
    //
    "override_default_login" => env('OVERRIDE_DEFAULT_LOGIN', true),
    //this is the user model path
    "user_model" => App\User::class,
    //this is where to redirect users if theirs login succeed ( user route name only )
    "redirect_success" => "home",
    //this is where to redirect users if theirs login fails
    "redirect_fail" => "/",
    //if azure Active Directory is off , allow laravel auth registration ?
    "allow_registration" => env('ALLOW_REGISTRATION', false),

];
