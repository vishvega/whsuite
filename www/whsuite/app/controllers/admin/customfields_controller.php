<?php
/**
 * Custom Fields Admin Controller
 *
 * The Custom Fields Admin controller handles the 'CRUD' operations for custom
 * fields. Note that some parts of this controller are only accessible when dev
 * mode is enabled.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class CustomfieldsController extends AdminController
{
    /**
     * View Groups
     *
     * Lists all custom field groups. If dev mode is active, will also show hidden
     * groups.
     */
    public function viewGroups($page = 1)
    {
        if (DEV_MODE) {
            $groups = DataGroup::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, array(), 'name', 'desc', 'admin-custom-fields-list-paging');
        } else {
            // Since we're not running in dev mode, we dont want to show non-editable
            // groups or groups that are not active.
            $conditions = array(
                array(
                    'column' => 'is_editable',
                    'operator' => '=',
                    'value' => '1'
                ),
                array(
                    'column' => 'is_active',
                    'operator' => '=',
                    'value' => '1'
                )
            );
            $groups = DataGroup::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'name', 'desc', 'admin-custom-fields-list-paging');
        }

        $title = $this->lang->get('custom_field_management');
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

        $this->view->set('groups', $groups);

        if (DEV_MODE) {
            $toolbar = array(
                array(
                    'url_route'=> 'admin-custom-fields-new-group',
                    'icon' => 'fa fa-plus',
                    'label' => 'new_custom_field_group'
                ),
            );
            $this->view->set('toolbar', $toolbar);
        }

        $this->view->display('customfields/viewGroups.php');
    }

    /**
     * View Group
     *
     * View and manage an individual custom field group.
     *
     * @param int $id ID of the group to show
     */
    public function viewGroup($id)
    {
        $group = DataGroup::find($id);
        if (empty($group) || ($group->is_active == '0' && !DEV_MODE)) {
            return $this->redirect('admin-custom-fields');
        }

        if (\Whsuite\Inputs\Post::Get() && DEV_MODE) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Group'), DataGroup::$rules);

            if ($validator->fails()) {
                // The validation failed. Find out why and send the user back to the group page and show the error(s)
                App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
                return $this->redirect('admin-custom-fields-view-group', ['id' => $group->id]);
            }

            $post_data = \Whsuite\Inputs\Post::get('Group');

            // Check that the slug isnt being used elsewhere.
            $slug_check = DataGroup::where('slug', '=', $post_data['slug'])->where('id', '!=', $group->id)->get();
            if ($slug_check->count() > 0) {
                // It's already in use, we cant let them use this slug.
                App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
                return $this->redirect('admin-custom-fields-view-group', ['id' => $group->id]);
            }

            $group->slug = $post_data['slug'];
            $group->name = $post_data['name'];
            $group->addon_id = $post_data['addon_id'];
            $group->is_editable = $post_data['is_editable'];
            $group->is_active = $post_data['is_active'];

            if ($group->save()) {
                App::get('session')->setFlash('success', $this->lang->get('scaffolding_save_success'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
            }

            return $this->redirect('admin-custom-fields-view-group', ['id' => $group->id]);
        } else {
            \Whsuite\Inputs\Post::set('Group', $group->toArray());
            $title = $this->lang->get($group->name);
            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($this->lang->get('custom_field_management'), 'admin-custom-fields');
            App::get('breadcrumbs')->add($title);
            App::get('breadcrumbs')->build();

            $this->view->set('title', $title);

            $this->view->set('group', $group);
            $this->view->set('fields', $group->DataField()->orderBy('sort', 'desc')->get());
            $this->view->set('addons', Addon::formattedList('id', 'directory', array(), 'directory', 'desc', true));

            $toolbar = array(
                array(
                    'url_route'=> 'admin-custom-fields-new-field',
                    'route_params' => array('id' => $group->id),
                    'icon' => 'fa fa-plus',
                    'label' => 'new_custom_field'
                ),
            );
            $this->view->set('toolbar', $toolbar);

            $this->view->display('customfields/viewGroup.php');
        }
    }

    /**
     * New Group
     *
     * Create a new custom field group.
     *
     */
    public function newGroup()
    {
        if (!DEV_MODE) {
            return $this->redirect('admin-custom-fields');
        }

        if (\Whsuite\Inputs\Post::Get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Group'), DataGroup::$rules);

            if ($validator->fails()) {
                // The validation failed. Find out why and send the user back to the new group page and show the error(s)
                App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
                return $this->redirect('admin-custom-fields-new-group');
            }

            $post_data = \Whsuite\Inputs\Post::get('Group');

            // Check that the slug isnt being used elsewhere.
            $slug_check = DataGroup::where('slug', '=', $post_data['slug'])->get();
            if ($slug_check->count() > 0) {
                // It's already in use, we cant let them use this slug.
                App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
                return $this->redirect('admin-custom-fields-new-group');
            }

            $group = new DataGroup();
            $group->slug = $post_data['slug'];
            $group->name = $post_data['name'];
            $group->addon_id = $post_data['addon_id'];
            $group->is_editable = $post_data['is_editable'];
            $group->is_active = $post_data['is_active'];

            if ($group->save()) {
                App::get('session')->setFlash('success', $this->lang->get('scaffolding_save_success'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
            }

            return $this->redirect('admin-custom-fields-view-group', ['id' => $group->id]);
        } else {
            $title = $this->lang->get('new_custom_field_group');
            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($this->lang->get('custom_field_management'), 'admin-custom-fields');
            App::get('breadcrumbs')->add($title);
            App::get('breadcrumbs')->build();

            $this->view->set('title', $title);

            $this->view->set('addons', Addon::formattedList('id', 'directory', array(), 'directory', 'desc', true));

            $this->view->display('customfields/newGroup.php');
        }
    }

    /**
     * Delete Group
     *
     * Delete a custom field group and all it's fields and field values.
     *
     * @param int $id ID of the group to delete
     */
    public function deleteGroup($id)
    {
        $group = DataGroup::find($id);
        if (empty($group) || !DEV_MODE) {
            return $this->redirect('admin-custom-fields');
        }

        // Delete the fields and field values
        $fields = $group->DataField()->get();
        foreach ($fields as $field) {
            $field->DataFieldValue()->delete();

            $field->delete();
        }

        if ($group->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('scaffolding_delete_success'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('scaffolding_delete_error'));
        }

        return $this->redirect('admin-custom-fields');
    }

    /**
     * New Custom Field
     *
     * Add a new field to a custom field group.
     *
     * @param int $id ID of the group to add the new field to.
     */
    public function newField($id)
    {
        $group = DataGroup::find($id);
        if (empty($group) || ($group->is_editable == '0' && ! DEV_MODE)) {
            return $this->redirect('admin-custom-fields');
        }

        if (\Whsuite\Inputs\Post::Get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Field'), DataField::$rules);

            if ($validator->fails()) {
                // The validation failed. Find out why and send the user back to the new group page and show the error(s)
                App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
                return $this->redirect('admin-custom-fields-new-field', ['id' => $group->id]);
            }

            $post_data = \Whsuite\Inputs\Post::get('Field');

            // Check that the slug isnt being used by another field within this group.
            $slug_check = DataField::where('slug', '=', $post_data['slug'])->where('data_group_id', '=', $group->id)->get();
            if ($slug_check->count() > 0 || !in_array($post_data['type'], DataField::$field_types)) {
                // Either the slug is in use, or the field type was invalid.
                App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
                return $this->redirect('admin-custom-fields-new-field', ['id' => $group->id]);
            }

            $field = new DataField();
            $field->slug = $post_data['slug'];
            $field->data_group_id = $group->id;
            $field->title = $post_data['title'];
            $field->type = $post_data['type'];
            $field->help_text = $post_data['help_text'];
            $field->placeholder = $post_data['placeholder'];
            $field->value_options = $post_data['value_options'];
            $field->is_editable = $post_data['is_editable'];
            $field->is_staff_only = $post_data['is_staff_only'];
            $field->validation_rules = $post_data['validation_rules'];
            $field->custom_regex = $post_data['custom_regex'];
            $field->sort = $post_data['sort'];

            if ($field->save()) {
                App::get('session')->setFlash('success', $this->lang->get('scaffolding_save_success'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
            }

            return $this->redirect('admin-custom-fields-view-field', ['id' => $group->id]);
        } else {
            $title = $this->lang->get('new_custom_field');
            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($this->lang->get('custom_field_management'), 'admin-custom-fields');
            App::get('breadcrumbs')->add($this->lang->get($group->name), 'admin-custom-fields-view-group', array('id' => $group->id));
            App::get('breadcrumbs')->add($title);
            App::get('breadcrumbs')->build();

            $field_types = array();
            foreach (DataField::$field_types as $type) {
                $field_types[$type] = $this->lang->get($type);
            }

            $this->view->set('field_types', $field_types);

            $this->view->set('title', $title);

            $this->view->set('group', $group);

            $this->view->display('customfields/fieldForm.php');
        }
    }

    /**
     * Edit Custom Field
     *
     * Edit an existing field within a group.
     *
     * @param int $id ID of the group that the field is associated with
     * @param  int $field_id ID of the custom field record
     */
    public function editField($id, $field_id)
    {
        $group = DataGroup::find($id);
        $field = DataField::find($field_id);
        if (empty($group) || empty($field) || ($group->is_editable == '0' && !DEV_MODE)) {
            return $this->redirect('admin-custom-fields');
        }

        if (\Whsuite\Inputs\Post::Get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Field'), DataField::$rules);

            if ($validator->fails()) {
                // The validation failed. Find out why and send the user back to the new group page and show the error(s)
                App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
                return $this->redirect('admin-custom-fields-new-field', ['id' => $group->id]);
            }

            $post_data = \Whsuite\Inputs\Post::get('Field');

            // Check that the slug isnt being used by another field within this group.
            $slug_check = DataField::where('slug', '=', $post_data['slug'])->where('data_group_id', '=', $group->id)->where('id', '!=', $field->id)->get();
            if ($slug_check->count() > 0 || !in_array($post_data['type'], DataField::$field_types)) {
                // Either the slug is in use, or the field type was invalid.
                App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
                return $this->redirect('admin-custom-fields-edit-field', ['id' => $group->id, 'field_id' => $field->id]);
            }

            $field->slug = $post_data['slug'];
            $field->title = $post_data['title'];
            $field->type = $post_data['type'];
            $field->help_text = $post_data['help_text'];
            $field->placeholder = $post_data['placeholder'];
            $field->value_options = $post_data['value_options'];
            $field->is_editable = $post_data['is_editable'];
            $field->is_staff_only = $post_data['is_staff_only'];
            $field->validation_rules = $post_data['validation_rules'];
            $field->custom_regex = $post_data['custom_regex'];
            $field->sort = $post_data['sort'];

            if ($field->save()) {
                App::get('session')->setFlash('success', $this->lang->get('scaffolding_save_success'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('scaffolding_save_error'));
            }

            return $this->redirect('admin-custom-fields-view-field', ['id' => $group->id]);
        } else {
            $title = $this->lang->get('edit_custom_field');
            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($this->lang->get('custom_field_management'), 'admin-custom-fields');
            App::get('breadcrumbs')->add($this->lang->get($group->name), 'admin-custom-fields-view-group', array('id' => $group->id));
            App::get('breadcrumbs')->add($title);
            App::get('breadcrumbs')->build();

            $field_types = array();
            foreach (DataField::$field_types as $type) {
                $field_types[$type] = $this->lang->get($type);
            }

            \Whsuite\Inputs\Post::set('Field', $field->toArray());

            $this->view->set('field', $field);
            $this->view->set('field_types', $field_types);

            $this->view->set('title', $title);

            $this->view->set('group', $group);

            $this->view->display('customfields/editFieldForm.php');
        }
    }

    /**
     * Delete Custom Field
     *
     * Delete a field within a group.
     *
     * @param int $id ID of the group that the field is associated with
     * @param  int $field_id ID of the custom field record
     */
    public function deleteField($id, $field_id)
    {
        $group = DataGroup::find($id);
        $field = DataField::find($field_id);
        if (empty($field) || empty($group) || !DEV_MODE) {
            return $this->redirect('admin-custom-fields');
        }

        // Delete the field and field values
        foreach ($field->DataFieldValue() as $value) {
            $value->delete();
        }

        if ($field->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('scaffolding_delete_success'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('scaffolding_delete_error'));
        }
        return $this->redirect('admin-custom-fields-view-field', ['id' => $group->id]);
    }
}
