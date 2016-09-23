<?php

class StaffGroup extends AppSentryGroup
{
    protected $table = 'staff_groups';

    public static $rules = array(

        'name' => 'required'
    );

    // relationship for us to use.
    public function Staff()
    {
        return $this->belongsToMany('Staff');
    }

    // slight sentry annoyance.
    // override user relation to allow it to delete staff / staff group relationships
    // as we have renamed all the users / groups for "staff"
    public function users()
    {
        return $this->belongsToMany('Staff');
    }

}
