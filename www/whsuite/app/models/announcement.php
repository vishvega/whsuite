<?php

class Announcement extends AppModel
{

    public static $rules = array(
        'title' => 'required|max:100',
        'body' => 'required',
        'is_published' => 'min:0|max:1',
        'clients_only' => 'min:0|max:1'
    );

    public function Language()
    {
        return $this->belongsTo('Language');
    }

    public static function paginate($per_page, $page, $conditions = array(), $sort_by = 'publish_date', $sort_order = 'desc', $route = null, $params = array())
    {
        return parent::paginate($per_page, $page, $conditions, $sort_by, $sort_order, $route, $params);
    }

}
