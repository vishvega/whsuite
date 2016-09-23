<?php

use \Whsuite\Inputs\Post as PostInput;

/**
 * Admin Dashboard Controller
 *
 * The dashboard controller handles the admin dashboard display, including the
 * retrieval of information to show in the dashboard panels.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class StaffsController extends AdminController
{
    public $route = null;

    public function onLoad()
    {
        parent::onLoad();

        $route = \App::get('dispatcher')->getRoute();
        $this->route = $route->values;
    }

    protected function indexToolbar()
    {
        return array(
            array(
                'url_route' => 'admin-staffgroup',
                'link_class' => '',
                'icon' => 'fa fa-group',
                'label' => 'staffgroup_management'
            ),
            array(
                'url_route' => 'admin-staff-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'staff_add'
            )
        );
    }

    protected function indexColumns()
    {
        return array(
            array(
                'field' => array(
                    'first_name',
                    'last_name'
                ),
                'label' => 'name',
            ),
            array(
                'field' => 'email'
            ),
            array(
                'field' => 'StaffGroup.name',
                'label' => 'Groups',
                'separator' => ' / '
            ),
            array(
                'field' => 'activated',
                'label' => 'status',
                'option_labels' => array(
                    '0' => '<span class="label label-danger">' . $this->lang->get('inactive') . '</span>',
                    '1' => '<span class="label label-success">' . $this->lang->get('active') . '</span>'
                )
            ),
            array(
                'field' => 'last_login'
            ),
            array(
                'action' => 'edit',
                'label' => null
            ),
            array(
                'action' => 'delete',
                'label' => null
            )
        );
    }

    protected function indexActions()
    {
        return array(
            'edit' => array(
                'url_route' => 'admin-staff-edit',
                'link_class' => 'btn btn-primary btn-small pull-right',
                'icon' => 'fa fa-pencil',
                'label' => 'edit',
                'params' => array('id')
            ),
            'delete' => array(
                'url_route' => 'admin-staff-delete',
                'link_class' => 'btn btn-danger btn-small pull-right',
                'icon' => 'fa fa-remove',
                'label' => 'delete',
                'params' => array('id')
            )
        );
    }

    protected function formFields()
    {
        $fields = array(
            'Staff.id',
            'Staff.first_name',
            'Staff.last_name',
            'Staff.email',
            'Staff.password' => array(
                'type' => 'password'
            ),
            'Staff.confirm_password' => array(
                'type' => 'password',
                'after' => '<span class="help-block">'.\App::get('translation')->get('password_leave_blank').'</span>'
            ),
            'Staff.language_id' => array(
                'type' => 'select',
                'label' => 'language'
            )
        );

        if (isset($this->route['action']) && $this->route['action'] != 'profile') {
            $fields[] = 'Staff.activated';
            $fields[] = 'Staff.StaffGroup';
        }

        return $fields;
    }


    protected function formToolbar($main_model)
    {
        if (isset($this->route['action']) && $this->route['action'] != 'profile') {
            return parent::formToolbar($main_model);
        } else {
            return array();
        }
    }

    protected function formBreadcrumb($model, $page_title)
    {
        if (isset($this->route['action']) && $this->route['action'] != 'profile') {
            return parent::formBreadcrumb($model, $page_title);
        } else {
            return array();
        }
    }

    protected function getExtraData($model)
    {
        // get the languages
        $language_id = Language::formattedList(
            'id',
            'name',
            array(
                array(
                    'column' => 'is_active',
                    'operator' => '=',
                    'value' => 1
                )
            )
        );

        $set_data = array(
            'language_id'
        );

        if (isset($this->route['action']) && $this->route['action'] != 'profile') {
            // get the staff groups
            $groups = $this->admin_auth->findAllGroups();

            $set_data[] = 'groups';
        } else {
            // dashboard widgets
            $selected_widgets = $model->Widget()->get();
            $available_widgets = Widget::formattedList();

            foreach ($selected_widgets as $widget) {
                if (isset($available_widgets[$widget->id])) {
                    unset($available_widgets[$widget->id]);
                }
            }

            $set_data[] = 'selected_widgets';
            $set_data[] = 'available_widgets';

            // dashboard shortcuts
            $selected_shortcuts = $model->Shortcut()->get();
            $available_shortcuts = Shortcut::formattedList();

            foreach ($selected_shortcuts as $shortcut) {
                if (isset($available_shortcuts[$shortcut->id])) {
                    unset($available_shortcuts[$shortcut->id]);
                }
            }

            $set_data[] = 'selected_shortcuts';
            $set_data[] = 'available_shortcuts';
        }

        $this->view->set(
            compact($set_data)
        );
    }

    protected function processData($field, $data, &$main_model)
    {
        if ($field == 'password' && empty($data)) {
            $data = false;
        } elseif ($field == 'confirm_password') {
            $data = false;
        } elseif ($field == 'StaffGroup') {
            $data = false;
        }

        return $data;
    }

    protected function afterSave(&$main_model)
    {
        if (isset($this->route['action']) && $this->route['action'] != 'profile') {
            $groups = $this->admin_auth->findAllGroups();
            $data = PostInput::get('data.Staff.StaffGroup');

            foreach ($groups as $group) {
                if (isset($data[$group->id]) && $data[$group->id] == 1) {
                    $main_model->addGroup($group);
                } else {
                    $main_model->removeGroup($group);
                }
            }
        } else {
            // Sync the dashboard shortcuts
            $shortcuts = PostInput::get('data.Staff.Shortcut');
            $sync = array();

            if (! empty($shortcuts)) {
                $ex = explode(',', $shortcuts);

                foreach ($ex as $sort => $shortcut_id) {
                    $sync[$shortcut_id] = array(
                        'sort' => $sort
                    );
                }
            }
            $main_model->Shortcut()->sync($sync);

            // sync the dashboard widgets
            $widgets = PostInput::get('data.Staff.Widget');
            $sync = array();

            if (! empty($widgets)) {
                $ex = explode(',', $widgets);

                foreach ($ex as $sort => $widget_id) {
                    $sync[$widget_id] = array(
                        'sort' => $sort
                    );
                }
            }

            $main_model->Widget()->sync($sync);
        }
    }

    public function form($id = null)
    {
        // override the form template
        $this->render_view = 'staff/form.php';

        // get the users selected groups if editing
        if (! is_null($id) && ! is_array(PostInput::get('data.Staff.StaffGroup'))) {
            $staff = Staff::find($id);
            $groups = $staff->getGroups();
            $staff_groups = array();
            foreach ($groups as $group) {
                $staff_groups[$group->id] = 1;
            }

            PostInput::set('data.Staff.StaffGroup', $staff_groups);
        }

        return parent::form($id);
    }

    public function delete($id)
    {
        if ($id != '1') {
            return parent::delete($id);
        } else {
            App::get('session')->setFlash('error', $this->lang->get('cant_delete_primary_staff_user'));
            return $this->redirect('admin-staffgroup');
        }
    }

    public function profile()
    {
        $this->render_view = 'staff/profile.php';
        $id = $this->admin_user->id;
        $this->assets->addScript('staff-profile.js');

        // get the users dashboard widgets
        $return = parent::form($id);

        if (array_search('Location: ' . App::get('router')->generate('admin-staff'), headers_list())) {
            return $this->redirect('admin-staff-myprofile');
        }
    }
}
