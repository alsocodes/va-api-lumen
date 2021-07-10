<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider as UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;


class CustomUserProvider extends UserProvider {

    public function validateCredentials(UserContract $user, array $credentials)
    {
        $client_secret = $credentials['client_secret'];
        return $client_secret === $user->getClientSecret();
        // return $this->hasher->check($plain, $user->getAuthPassword());
        // echo '<pre>';
        // print_r($user->getClientSecret());
        // return false;
    }

}