<?php

namespace App\Libraries;

class ClientAuth
{
    public static function auth()
    {
        $hasher = new \Cartalyst\Sentry\Hashing\NativeHasher;
        $userProvider = new \Cartalyst\Sentry\Users\Eloquent\Provider($hasher, '\Client');
        $groupProvider = new \Cartalyst\Sentry\Groups\Eloquent\Provider('\ClientGroup');
        $throttleProvider = new \Cartalyst\Sentry\Throttling\Eloquent\Provider($userProvider, '\ClientThrottle');

        $options = array();
        $key = 'whsuite_client';

        $session = new \Cartalyst\Sentry\Sessions\NativeSession($key);
        $cookie = new \Cartalyst\Sentry\Cookies\NativeCookie($options, $key);

        return \Cartalyst\Sentry\Facades\Native\Sentry::createSentry(
            $userProvider,
            $groupProvider,
            $throttleProvider,
            $session,
            $cookie
        );
    }
}
