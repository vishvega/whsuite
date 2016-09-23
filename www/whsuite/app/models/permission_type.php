<?php
class PermissionType extends AppModel
{

    public function children()
    {
        return $this->hasMany('PermissionType', 'parent_id');
    }

}
