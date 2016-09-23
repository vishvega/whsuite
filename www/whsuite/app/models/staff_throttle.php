<?php

class StaffThrottle extends AppSentryThrottle
{
    protected $table = 'staff_throttles';

    public function user()
    {
        return $this->belongsTo('Staff', 'user_id');
    }
}