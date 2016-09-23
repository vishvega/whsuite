<?php

class Staff extends AppSentryUser
{
    protected $table = 'staffs';

    public static $rules = array(
        'email' => 'email|required',
        'password' => 'same:confirm_password',
        'activated' => 'integer'
    );

    // relationship for us to use.
    public function StaffGroup()
    {
        return $this->belongsToMany('StaffGroup');
    }

    // slight sentry annoyance.
    // override group relation to allow it to get groups
    public function groups()
    {
        return $this->belongsToMany('StaffGroup');
    }

    public function Shortcut()
    {
        return $this->belongsToMany('Shortcut')
            ->orderBy('sort', 'asc')
            ->where('is_active', '=', 1)
            ->withPivot('sort');
    }

    public function Widget()
    {
        return $this->belongsToMany('Widget')
            ->orderBy('sort', 'asc')
            ->where('is_active', '=', 1)
            ->withPivot('sort');
    }

    public static function all($columns = array('*'))
    {
        $staff = parent::all($columns);

        foreach ($staff as $id => $s) {
            if ($s['last_login']) {

                $Carbon = \Carbon\Carbon::parse(
                    $s['last_login'],
                    \App::get('configs')->get('settings.localization.timezone')
                );

                $staff[$id]['last_login'] = $CarbonRenewal
                    ->format(\App::get('configs')->get('settings.localization.short_datetime_format'));
            } else {
                $staff[$id]['last_login'] = App::get('translation')->get('not_available');
            }
        }

        return $staff;
    }


    /**
     * get this staff users dashboard setup
     *
     *
     * @return  Object  dashboard widgets to display
     */
    public function getDashboardWidgets()
    {
        return $this->Widget()->get();
    }

    /**
     * get this staff users shortcut setup
     *
     *
     * @return  Object  dashboard widgets to display
     */
    public function getDashboardShortcuts()
    {
        return $this->Shortcut()->get();
    }

}
