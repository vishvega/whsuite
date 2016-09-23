<?php
/**
 * Servers Admin Controller
 *
 * The servers admin controller handles all CRUD operations for servers, server
 * groups and ip assignment to servers.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class ServersController extends AdminController
{
    /**
     * List Servers
     */
    public function listServers()
    {
        $title = $this->lang->get('server_management');

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($title);

        App::get('breadcrumbs')->build();
        $this->view->set('title', $title);
        $this->view->set('server_groups', ServerGroup::all());

        $toolbar = array(
            array(
                'url_route'=> 'admin-servergroup-add',
                'icon' => 'fa fa-plus',
                'label' => 'servergroup_add'
            ),
        );
        $this->view->set('toolbar', $toolbar);

        $this->view->display('servers/listServers.php');
    }

    /**
     * New Group
     *
     * Add a new server group
     */
    public function newGroup()
    {
        $title = $this->lang->get('servergroup_add');

        if (\Whsuite\Inputs\Post::get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Group'), ServerGroup::$rules);
            $group = new ServerGroup();

            if ($validator->fails()) {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            } elseif (!$group->validateCustomFields(false)) {
                // For now we set this as a flash error. In a future update we'll prevent the need to redirect/reload.
                App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
                return $this->redirect('admin-servergroup-add');
            } else {
                $group_data = \Whsuite\Inputs\Post::get('Group');

                $group->name = $group_data['name'];
                $group->description = $group_data['description'];
                $group->autofill = $group_data['autofill'];
                $group->server_module_id = $group_data['server_module_id'];

                if ($group->save() && $group->saveCustomFields(false)) {
                    App::get('session')->setFlash('success', $this->lang->get('scaffolding_save_success'));
                    return $this->redirect('admin-servergroup-manage', ['id' => $group->id]);
                } else {
                    \App\Libraries\Message::set($this->lang->get('scaffolding_save_error'), 'fail');
                }

                \Whsuite\Inputs\Post::set('Group', $group->toArray());
            }
        }

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('server_management'), 'admin-server');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('group', new ServerGroup());

        $this->view->set('server_modules', ServerModule::formattedList('id', 'name', array(), 'name', 'desc', true));

        $this->view->display('servers/newGroup.php');
    }

    /**
     * Manage Group
     *
     * Manage a server group.
     *
     * @param int $id ID of the group to manage
     */
    public function manageGroup($id)
    {
        $group = ServerGroup::find($id);

        if (empty($group)) {
            return $this->redirect('admin-server');
        }

        $title = $this->lang->get('server_group').' - '.$group->name;

        if (\Whsuite\Inputs\Post::get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Group'), ServerGroup::$rules);

            if (! $validator->fails()) {
                if ($group->validateCustomFields(false)) {
                    $group_data = \Whsuite\Inputs\Post::get('Group');

                    $group->name = $group_data['name'];
                    $group->description = $group_data['description'];
                    $group->autofill = $group_data['autofill'];
                    $group->server_module_id = $group_data['server_module_id'];
                    $group->default_server_id = $group_data['default_server_id'];

                    if ($group->save() && $group->saveCustomFields(false)) {
                        App::get('session')->setFlash('success', $this->lang->get('scaffolding_save_success'));

                        return $this->redirect('admin-servergroup-manage', ['id' => $group->id]);
                    } else {
                        \App\Libraries\Message::set($this->lang->get('scaffolding_save_error'), 'fail');
                    }
                } else {
                    // For now we set this as a flash error. In a future update we'll prevent the need to redirect/reload.
                    App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
                    return $this->redirect('admin-servergroup-manage', ['id' => $group->id]);
                }
            } else {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            }
        }

        \Whsuite\Inputs\Post::set('Group', $group->toArray());

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('server_management'), 'admin-server');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('group', $group);
        $this->view->set('servers', $group->Server()->orderByRaw('priority = 0, priority ASC')->get());

        $server_list_conditions = array(
            array(
                'type' => 'and',
                'column' => 'server_group_id',
                'operator' => '=',
                'value' => $group->id
            )
        );

        $this->view->set('server_list', Server::formattedList('id', 'name', $server_list_conditions, 'name', 'desc', true));

        $this->view->set('server_modules', ServerModule::formattedList('id', 'name', array(), 'name', 'desc', true));

        $toolbar = array(
            array(
                'url_route'=> 'admin-server-add',
                'route_params' => array('id' => $group->id),
                'icon' => 'fa fa-plus',
                'label' => 'server_add'
            ),
        );
        $this->view->set('toolbar', $toolbar);

        $this->view->display('servers/manageGroup.php');
    }

    /**
     * Delete Group
     *
     * Delete a server group. A group can only be deleted if its empty.
     *
     * @param int $id ID of the group to delete
     */
    public function deleteGroup($id)
    {
        $group = ServerGroup::find($id);
        $server_count = $group->Server()->count();
        if (empty($group) || $server_count > 0) {
            // The group either does not exist, or it has servers still assigned
            // to it, in which case it's not allowed to be deleted. Redirect back
            // to the server group listing page.
            return $this->redirect('admin-server');
        }

        if ($group->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('scaffolding_delete_success'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('scaffolding_delete_error'));
        }

        return $this->redirect('admin-server');
    }

    /**
     * New Server
     *
     * Creates a new server within a defined group
     *
     * @param int $id ID of the group to add the server to
     */
    public function newServer($id)
    {
        $group = ServerGroup::find($id);
        if (empty($group)) {
            // The group doesn't exist, redirect back to the server listing page.
            return $this->redirect('admin-server');
        }

        $server_module = $group->ServerModule()->first();
        if ($server_module) {
            $addon = $server_module->Addon()->first();
        }

        $title = $this->lang->get('server_add');

        $fail = false;

        if (\Whsuite\Inputs\Post::get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Server'), ServerGroup::$rules);

            if (!$validator->fails()) {
                if (!isset($addon) || App::factory('\Whsuite\CustomFields\CustomFields')->validateCustomFields('serverdata_'.$addon->directory, 0, false)) {
                    // Before we continue, we want to check that we can actually
                    // connect to the server. To do this we need to tell the server
                    // addon module all the details just entered, and we'll do that
                    // by running a test connection method in the addon if it exists.
                    // If it doesn't exist we'll skip this step as its down to the
                    // server addon module to do the check.
                    if (!isset($addon) || ! \App::checkInstalledAddon($addon->directory)) {
                        $addon = false;
                    }

                    if ($addon) {
                        $post_data = \Whsuite\Inputs\Post::get();

                        $server_helper = \App::factory('\App\Libraries\ServerHelper');

                        if (! $server_helper->testConnection($addon, $post_data)) {
                            \App\Libraries\Message::set($this->lang->get('server_connection_failed'), 'fail');
                            $fail = true;
                        }
                    }

                    // If we made it to this point, the form data was all present
                    // and correct. So lets go ahead an insert the data, and then
                    // redirect to the server page, where things like IP blocks
                    // can be added.
                    if (!$fail) {
                        $post_data = \Whsuite\Inputs\Post::get('Server');

                        $server = new Server();
                        $server->server_group_id = $group->id;
                        $server->name = $post_data['name'];
                        $server->hostname = $post_data['hostname'];
                        $server->main_ip = $post_data['main_ip'];
                        $server->location = $post_data['location'];
                        $server->username = App::get('security')->encrypt($post_data['username']);
                        $server->password = App::get('security')->encrypt($post_data['password']);
                        $server->api_key = App::get('security')->encrypt($post_data['api_key']);
                        $server->ssl_connection = $post_data['ssl_connection'];
                        $server->max_accounts = $post_data['max_accounts'];
                        $server->priority = $post_data['priority'];
                        $server->is_active = $post_data['is_active'];
                        $server->notes = App::get('security')->encrypt($post_data['notes']);
                        $server->status_url = $post_data['status_url'];

                        if ($server->save()) {
                            if (!$addon || App::factory('\Whsuite\CustomFields\CustomFields')->saveCustomFields('serverdata_'.$addon->directory, $server->id, false)) {
                                App::get('session')->setFlash('success', $this->lang->get('scaffolding_save_success'));

                                return $this->redirect('admin-server-manage', ['id' => $group->id, 'server_id' => $server->id]);
                            } else {
                                // Yes, I know the following code is being repeaded
                                // three times. We'll optimize this at a later stage ;)
                                App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));

                                return $this->redirect('admin-servergroup-manage', ['id' => $group->id]);
                            }
                        } else {
                            App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
                            return $this->redirect('admin-servergroup-manage', ['id' => $group->id]);
                        }
                    }
                }
            } else {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            }
        }

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('server_management'), 'admin-server');
        App::get('breadcrumbs')->add(
            $this->lang->get('server_group').' - '.$group->name,
            'admin-servergroup-manage',
            array('id' => $group->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('group', $group);

        $custom_fields = null;
        if (isset($addon)) {
            $custom_fields = App::factory('\Whsuite\CustomFields\CustomFields')->generateForm('serverdata_'.$addon->directory, 0, false);
        }

        $this->view->set('custom_fields', $custom_fields);

        $this->view->display('servers/newServer.php');
    }

    /**
     * Manage Server
     *
     * Handles the management side of the server. Part of this includes loading up
     * a management view from the server module if one is provided.
     *
     * @param int $id ID of the group that the server belongs to
     * @param int $server_id ID of the server to manage
     */
    public function manageServer($id, $server_id)
    {
        $group = ServerGroup::find($id);
        $server = Server::find($server_id);
        if (empty($group) || empty($server) || $server->server_group_id != $group->id) {
            return $this->redirect('admin-server');
        }

        $server_module = $group->ServerModule()->first();
        if ($server_module) {
            $addon = $server_module->Addon()->first();
        }

        unset($server->password);
        unset($server->api_key);

        if (\Whsuite\Inputs\Post::get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Server'), Server::$rules);

            if (! $validator->fails()) {
                if (! isset($addon) || App::factory('\Whsuite\CustomFields\CustomFields')->validateCustomFields('serverdata_'.$addon->directory, $server->id, false)) {
                    $server_data = \Whsuite\Inputs\Post::get('Server');

                    $server->name = $server_data['name'];
                    $server->hostname = $server_data['hostname'];
                    $server->main_ip = $server_data['main_ip'];
                    $server->location = $server_data['location'];
                    $server->username = App::get('security')->encrypt($server_data['username']);
                    if ($server_data['password'] !='') {
                        $server->password = App::get('security')->encrypt($server_data['password']);
                    }
                    if ($server_data['api_key'] !='') {
                        $server->api_key = App::get('security')->encrypt($server_data['api_key']);
                    }
                    $server->ssl_connection = $server_data['ssl_connection'];
                    $server->max_accounts = $server_data['max_accounts'];
                    $server->is_active = $server_data['is_active'];
                    $server->notes = App::get('security')->encrypt($server_data['notes']);
                    $server->status_url = $server_data['status_url'];
                    $server->priority = $server_data['priority'];

                    if ($server->save() && (!isset($addon) || App::factory('\Whsuite\CustomFields\CustomFields')->saveCustomFields('serverdata_'.$addon->directory, $server->id, false))) {
                        App::get('session')->setFlash('success', $this->lang->get('scaffolding_save_success'));

                        return $this->redirect('admin-server-manage', ['id' => $group->id, 'server_id' => $server->id]);
                    } else {
                        \App\Libraries\Message::set($this->lang->get('scaffolding_save_error'), 'fail');
                    }
                } else {
                    // For now we set this as a flash error. In a future update we'll prevent the need to redirect/reload.
                    App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
                    return $this->redirect('admin-server-manage', ['id' => $group->id, 'server_id' => $server->id]);
                }
            } else {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            }
        }

        $title = $this->lang->get('manage_server').' - '.$server->name.' ('.$server->main_ip.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('server_management'), 'admin-server');
        App::get('breadcrumbs')->add(
            $this->lang->get('server_group').' - '.$group->name,
            'admin-servergroup-manage',
            array('id' => $group->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('server', $server);
        $this->view->set('group', $group);
        $this->view->set('title', $title);
        $this->view->set('accounts', $server->Hosting()->get());
        $this->view->set('ip_addresses', $server->ServerIp()->get());
        $this->view->set('nameservers', $server->ServerNameserver()->get());

        // load in the custom field values.
        if (isset($addon)) {
            $this->view->set('addon_details', $addon->details());

            $this->view->set('manage_route', App::get('router')->generate('admin-server-manage-tab', array('id' => $group->id, 'server_id' => $server->id)));

            //$this->view->set('manage_route', App::get('router')->generate('admin-server-'.$addon->directory.'-manage', array('id' => $group->id, 'server_id' => $server->id)));

            $custom_field_data = App::factory('\Whsuite\CustomFields\CustomFields')->getGroup('serverdata_'.$addon->directory, $server->id, false);

            $this->view->set('custom_field_data', $custom_field_data);

            $custom_fields = App::factory('\Whsuite\CustomFields\CustomFields')->generateForm('serverdata_'.$addon->directory, $server->id, false);
            $this->view->set('custom_fields', $custom_fields);
        }

        \Whsuite\Inputs\Post::set('Server', $server->toArray());
        $this->view->display('servers/manageServer.php');
    }

    /**
     * Manage Server Tab
     *
     * This is the ajax tab used to show the server management details. This runs via the
     * server helper, and then hands off to the addon being used to handle the retrieval
     * of the server details.
     */
    public function manageServerTab($id, $server_id)
    {
        // Load the Server Helper
        $server_helper = \App::factory('\App\Libraries\ServerHelper');

        try {
            $server_details = $server_helper->getServerDetails($server_id);

            $this->view->set('server_details', $server_details);
            $this->view->display('servers/manageServer/manageServer.php');

        } catch (\Exception $e) {
            if ($e->getMessage() == 'no_management') {
                $error_message = $this->lang->get('server_no_management_options_available');
            } else {
                $error_message = $this->lang->get('server_connection_failed');
            }

            $this->view->set('error_message', $error_message);
            $this->view->display('servers/manageServer/errorMessage.php');
        }
    }

    /**
     * Delete Server
     *
     * Delete a server. Only works if there are no hosting accounts found on it.
     *
     * @param int $id ID of the group that the server belongs to
     * @param int $server_id ID of the server to delete
     */
    public function deleteServer($id, $server_id)
    {
        $group = ServerGroup::find($id);
        $server = Server::find($server_id);
        if (empty($group) || empty($server) || $server->server_group_id != $group->id) {
            return $this->redirect('admin-server');
        }

        // Check if the server has accounts
        $accounts = $server->Hosting()->get();
        if ($accounts->count() > 0) {
            return $this->redirect('admin-server');
        }


        $ip_addresses = $server->ServerIp();
        $nameservers = $server->ServerNameserver();

        if ($ip_addresses->count() > 0) {
            $ip_addresses->delete();
        }

        if ($nameservers->count() > 0) {
            $nameservers->delete();
        }

        if ($server->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('scaffolding_delete_success'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('scaffolding_delete_error'));
        }
        return $this->redirect('admin-server');
    }

    /**
     * Delete Ips
     *
     * Delete one or more IP addresses that are assigned to a server. IP's can
     * only be deleted if they are not assigned to a hosting account.
     *
     * @param int $id ID of the group that the server belongs to
     * @param int $server_id ID of the server to delete the IP's from.
     */
    public function deleteIps($id, $server_id)
    {
        $group = ServerGroup::find($id);
        $server = Server::find($server_id);
        if (empty($group) || empty($server) || $server->server_group_id != $group->id || !\Whsuite\Inputs\Post::get()) {
            return $this->redirect('admin-server');
        }

        $post_data = \Whsuite\Inputs\Post::get();

        $ip_ids = $post_data['ip'];

        foreach ($server->ServerIp()->get() as $ip) {
            if ($ip->product_purchase_id == '0' && array_key_exists($ip->id, $ip_ids) && $ip_ids[$ip->id] != '0') {
                $ip->delete();
            }
        }

        App::get('session')->setFlash('success', $this->lang->get('server_ips_updated'));
        return $this->redirect('admin-server-manage', ['id' => $group->id, 'server_id' => $server->id]);
    }

    /**
     * New Ip Range
     *
     * Add a new IP range for a server based on a starting IP and ending IP part.
     *
     * @param int $id ID of the group that the server belongs to
     * @param int $server_id ID of the server
     */
    public function newIpRange($id, $server_id)
    {
        $group = ServerGroup::find($id);
        $server = Server::find($server_id);
        if (empty($group) || empty($server) || $server->server_group_id != $group->id || !\Whsuite\Inputs\Post::get()) {
            return $this->redirect('admin-server');
        }

        $post_data = \Whsuite\Inputs\Post::get();
        $start_data = $post_data['startip'];
        $end_data = $post_data['endip'];

        $ips = array();

        for ($i=$start_data['d']; $i<=$end_data['d']; $i++) {
            $ips[] = $start_data['a'].'.'.$start_data['b'].'.'.$start_data['c'].'.'.$i;
        }

        // Our IP list has been built, now validate the ip' to ensure we have no
        // invalid addresses.
        if (! filter_var_array($ips, FILTER_VALIDATE_IP)) {
            App::get('session')->setFlash('error', $this->lang->get('invalid_ip_addresses_entered'));
            return $this->redirect('admin-server-manage', ['id' => $group->id, 'server_id' => $server->id]);
        }

        foreach ($ips as $ip) {
            // Check if the IP already exists in the database
            if (ServerIp::where('ip_address', '=', $ip)->count() >= 1) {
                App::get('session')->setFlash('error', $this->lang->get('ip_address_already_exists'));
                return $this->redirect('admin-server-manage', ['id' => $group->id, 'server_id' => $server->id]);
            }

            // At this point everything is ok - the IP is valid and it's not already
            // in the system. Lets go ahead and add it now.
            $new_ip = new ServerIp();
            $new_ip->server_id = $server->id;
            $new_ip->ip_address = $ip;
            if (! $new_ip->save()) {
                App::get('session')->setFlash('error', $this->lang->get('error_saving_ip_address'));
                return $this->redirect('admin-server-manage', ['id' => $group->id, 'server_id' => $server->id]);
            }
        }

        App::get('session')->setFlash('success', $this->lang->get('server_ips_updated'));
        return $this->redirect('admin-server-manage', ['id' => $group->id, 'server_id' => $server->id]);
    }

    /**
     * New Nameserver
     *
     * Create a new nameserver record for a server.
     *
     * @param int $id ID of the group that the server belongs to
     * @param int $server_id ID of the server
     */
    public function newNameserver($id, $server_id)
    {
        $group = ServerGroup::find($id);
        $server = Server::find($server_id);
        if (empty($group) || empty($server) || $server->server_group_id != $group->id || ! \Whsuite\Inputs\Post::get()) {
            return $this->redirect('admin-server');
        }

        $post_data = \Whsuite\Inputs\Post::get('Nameserver');

        $this->validator->extend('hostname', function ($attribute, $value, $parameters) {
            if (preg_match("/^(?:(.+)\.)?([^.]+\.[^.]+)$/", $value)) {
                return true;
            }
            return false;
        });

        $validator = $this->validator->make($post_data, ServerNameserver::$rules);

        if (! $validator->fails()) {
            $nameserver = new ServerNameserver();
            $nameserver->server_id = $server->id;
            $nameserver->hostname = $post_data['hostname'];
            $nameserver->ip_address = $post_data['ip_address'];

            if ($nameserver->save()) {
                App::get('session')->setFlash('success', $this->lang->get('server_nameservers_saved'));
            } else {
                App::get('session')->setFlash('success', $this->lang->get('error_saving_server_nameservers'));
            }
        } else {
            App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
        }

        return $this->redirect('admin-server-manage', ['id' => $group->id, 'server_id' => $server->id]);
    }

    /**
     * Delete Nameservers
     *
     * Delete one or more nameserver records from a server.
     *
     * @param int $id ID of the group that the server belongs to
     * @param int $server_id ID of the server
     */
    public function deleteNameservers($id, $server_id)
    {
        $group = ServerGroup::find($id);
        $server = Server::find($server_id);
        if (empty($group) || empty($server) || $server->server_group_id != $group->id || !\Whsuite\Inputs\Post::get()) {
            return $this->redirect('admin-server');
        }

        $post_data = \Whsuite\Inputs\Post::get();

        $ns_ids = $post_data['nameserver'];

        foreach ($server->ServerNameserver()->get() as $ns) {
            if (array_key_exists($ns->id, $ns_ids) && $ns_ids[$ns->id] != '0') {
                $ns->delete();
            }
        }

        App::get('session')->setFlash('success', $this->lang->get('server_nameservers_saved'));
        return $this->redirect('admin-server-manage', ['id' => $group->id, 'server_id' => $server->id]);
    }
}
