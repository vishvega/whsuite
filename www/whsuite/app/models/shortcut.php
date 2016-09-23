<?php

class Shortcut extends AppModel
{
    public function Staff()
    {
        return $this->belongsToMany('Staff')
            ->orderBy('sort', 'asc')
            ->where('is_active', '=', 1)
            ->withPivot('sort');
    }

}
