<?php

namespace App\Libraries;

class AdminAuth
{
    public static function auth()
    {
        $hasher = new \Cartalyst\Sentry\Hashing\NativeHasher;
        $userProvider = new \Cartalyst\Sentry\Users\Eloquent\Provider($hasher, '\Staff');
        $groupProvider = new \Cartalyst\Sentry\Groups\Eloquent\Provider('\StaffGroup');
        $throttleProvider = new \Cartalyst\Sentry\Throttling\Eloquent\Provider($userProvider, '\StaffThrottle');

        $options = array();
        $key = 'whsuite_staff';

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
