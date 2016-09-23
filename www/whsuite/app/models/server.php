<?php

class Server extends AppModel
{
    public static $rules = array(
        'server_group_id' => 'integer',
        'name' => 'required|max:255',
        'hostname' => 'required|max:255',
        'main_ip' => 'required|max:46',
        'location' => 'max:255',
        'ssl_connection' => 'min:0|max:1|integer',
        'max_accounts' =>'min:-1|integer',
        'is_active' => 'min:0|max:1|integer',
    );

    public static function formattedListHosting($active = true, $null_row = false)
    {
        if($active) {
            $list = parent::formattedList('id' , 'name',
                array(
                    array(
                        'type' => 'and',
                        'column' => 'is_active',
                        'operator' => '=',
                        'value' => '1'
                    )
                ),
                'name', 'desc', $null_row
            );
        } else {
            $list = parent::formattedList('id' , 'name', array(), 'sort', 'desc');
        }
        $server_list = array();
        foreach($list as $id => $name)
        {
            if($id > 0) {
                $server = Server::find($id);

                $group = ServerGroup::find($server->server_group_id);

                $server_list[$id] = $name.' ('.$group->name.')';
            } else {
                $server_list[$id] = $name;
            }
        }

        return $server_list;
    }

    public function ServerGroup()
    {
        return $this->belongsTo('ServerGroup');
    }

    public function ServerIp()
    {
        return $this->hasMany('ServerIp');
    }

    public function totalAccounts()
    {
        return Hosting::where('server_id', '=', $this->id)->where('status', '=', '1')->count();
    }

    public function ServerNameserver()
    {
        return $this->hasMany('ServerNameserver');
    }

    public function Hosting()
    {
        return $this->hasMany('Hosting');
    }
}
