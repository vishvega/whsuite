<?php

use \Illuminate\Support\Str;
use \Whsuite\Inputs\Post as PostInput;

class StaffGroupsController extends AdminController
{

    /**
     * scaffolding overrides for group listing
     *
     * see admin base controller for doc blocks
     */
    protected function indexToolbar()
    {
        return array(
            array(
                'url_route' => 'admin-staff',
                'link_class' => '',
                'icon' => 'fa fa-user',
                'label' => 'staff_management'
            ),
            array(
                'url_route' => 'admin-staffgroup-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'staffgroup_add'
            )
        );
    }

    protected function indexColumns()
    {
        return array(
            array(
                'field' => 'name'
            ),
            array(
                'field' => 'updated_at'
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
                'url_route' => 'admin-staffgroup-edit',
                'link_class' => 'btn btn-primary btn-small pull-right',
                'icon' => 'fa fa-pencil',
                'label' => 'edit',
                'params' => array('id')
            ),
            'delete' => array(
                'url_route' => 'admin-staffgroup-delete',
                'link_class' => 'btn btn-danger btn-small pull-right',
                'icon' => 'fa fa-remove',
                'label' => 'delete',
                'params' => array('id')
            )
        );
    }

    protected function indexBreadcrumb($model, $page_title)
    {
        $breadcrumb = App::get('breadcrumbs');
        $breadcrumb->add($this->lang->get('dashboard'), 'admin-home');
        $breadcrumb->add($this->lang->get('staff_management'), 'admin-staff');
        $breadcrumb->add($page_title);
        $breadcrumb->build();
    }

    protected function formBreadcrumb($model, $page_title)
    {
        $breadcrumb = App::get('breadcrumbs');
        $breadcrumb->add($this->lang->get('dashboard'), 'admin-home');
        $breadcrumb->add($this->lang->get('staff_management'), 'admin-staff');
        $breadcrumb->add($this->lang->get(strtolower($model) . '_management'), 'admin-' . strtolower($model));
        $breadcrumb->add($page_title);
        $breadcrumb->build();
    }

    protected function formFields()
    {
        $fields = array(
            'StaffGroup.id',
            'StaffGroup.name',
            'StaffGroup.permissions'
        );

        return $fields;
    }

    protected function processData($field, $data, &$main_model)
    {
        if ($field == 'permissions') {
            // We need to tell Sentry all of the permission slugs - if they are 'checked'
            // we give the value a '1', otherwise a '0'. The zero has to be issued as otherwise
            // old permissions would never be removed.
            $new_permissions = array();

            foreach (PermissionType::all() as $permission) {
                if (isset($data[$permission->slug])) {
                    $new_permissions[$permission->slug] = $data[$permission->slug];
                } else {
                    $new_permissions[$permission->slug] = 0;
                }
            }
            $data = $new_permissions; // Apply the newly created permission array
        }

        return $data;
    }

    protected function getExtraData($model)
    {
        $this->view->set('permissions', PermissionType::where('parent_id', '=', '0')->get());
    }

    /**
     * form function override for template
     */
    public function form($id = null)
    {
        // override
        $this->render_view = 'staff_groups/form.php';

        return parent::form($id);
    }

    /**
     * delete function override to protect group ID 1
     */
    public function delete($id)
    {
        if ($id != '1') {
            return parent::delete($id);
        } else {
            App::get('session')->setFlash('error', $this->lang->get('cant_delete_primary_staff_group'));
            return $this->redirect('admin-staffgroup');
        }
    }
}
