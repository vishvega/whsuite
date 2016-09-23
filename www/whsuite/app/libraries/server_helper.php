<?php
namespace App\Libraries;

class ServerHelper {

    public $server;
    public $server_group;
    public $server_module;
    public $addon = false;

    public $library;

    public function initAddon($server_id)
    {
        $this->server = \Server::find($server_id);
        $this->server_group = $this->server->ServerGroup()->first();

        $this->server_module = $this->server_group->ServerModule()->first();

        if ($this->server_module) {
            $this->addon = $this->server_module->Addon()->first();
        }

        // Load the addon library if one exists
        if (isset($this->addon->directory)) {
            $this->library = \App::factory('Addon\\'.$this->addon->directory.'\Libraries\\'.ucfirst($this->addon->directory));
            $this->library->initServer($this->server, $this->server_group, $this->server_module);
        }

    }

    public function testConnection($addon, $server_data)
    {
        $this->addon = $addon;

        if (isset($this->addon->directory)) {
            $this->library = \App::factory('Addon\\'.$this->addon->directory.'\Libraries\\'.ucfirst($this->addon->directory));

            try {
                return $this->library->testConnection($server_data);
            } catch(\Exception $e) {
                return false;
            }
        }
        return false;
    }

    public function getServerDetails($server_id)
    {
        $this->initAddon($server_id);

        if ($this->library) {

            return $this->library->serverDetails();

        }

        throw new \Exception('server_connection_failed');
    }

    public function productFields()
    {
        if ($this->library) {

            return $this->library->productFields();

        }

        throw new \Exception('server_connection_failed');
    }

    public function defaultServer($group)
    {
        if (!is_object($group)) {
            $group = \ServerGroup::find($group);
        }
        if (empty($group) || !isset($group->id) || $group->id <= 0) {
            return false;
        }

        $order_string = '';

        if($group->default_server_id > 0) {
            $order_string .='id = '.$group->default_server_id.' desc,';
        }

        $order_string .='priority = 0, priority asc';

        $server = \Server::where('server_group_id', '=', $group->id)->where('is_active', '=', '1')->orderByRaw($order_string)->first();

        if(!empty($server) && isset($server->id)) {
            $this->initAddon($server->id);
            return $server;
        }
        return null;
    }

    public function createService($purchase, $hosting)
    {
        try {
            $result = $this->library->createService($purchase, $hosting);

            if (! $result) {
                return false;
            }

            return $result;

        } catch(\Exception $e) {
            return false;
        }
    }

    public function renewService($purchase, $hosting)
    {
        $this->initAddon($hosting->server_id);

        try {
            $result = $this->library->renewService($purchase, $hosting);
            if (! $result) {
                return false;
            }

            return true;

        } catch(\Exception $e) {
            return false;
        }
    }

    public function terminateService($purchase, $hosting)
    {
        $this->initAddon($hosting->server_id);

        try {
            $result = $this->library->terminateService($purchase, $hosting);
            if (! $result) {
                return false;
            }

            return true;

        } catch(\Exception $e) {
            return false;
        }
    }

    public function suspendService($purchase, $hosting)
    {
        $this->initAddon($hosting->server_id);

        try {
            $result = $this->library->suspendService($purchase, $hosting);
            if (! $result) {
                return false;
            }

            $purchase->status = '2';
            $purchase->save();

            return true;

        } catch(\Exception $e) {
            return false;
        }
    }

    public function unsuspendService($purchase, $hosting)
    {
        $this->initAddon($hosting->server_id);

        try {
            $result = $this->library->unsuspendService($purchase, $hosting);
            if (! $result) {
                return false;
            }

            $purchase->status = '1';
            $purchase->save();

            return true;

        } catch(\Exception $e) {
            return false;
        }
    }

}