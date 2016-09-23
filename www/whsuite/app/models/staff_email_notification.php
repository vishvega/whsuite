<?php

class StaffEmailNotification extends AppModel
{

    public function Staff()
    {
        return $this->belongsTo('Staff');
    }

    public function EmailTemplate()
    {
        return $this->belongsTo('EmailTemplate');
    }

}
