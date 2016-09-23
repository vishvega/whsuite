<?php
/**
 * Admin Announcement Controller
 *
 * The announcement controller provides a way for staff to add, edit and delete
 * announcements that will show in the client area.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class AnnouncementController extends AdminController
{

    protected function indexToolbar()
    {
        return array(
            array(
                'url_route' => 'admin-announcement',
                'link_class' => '',
                'icon' => 'fa fa-list-ul',
                'label' => 'announcement_management'
            ),
            array(
                'url_route' => 'admin-announcement-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'announcement_add'
            )
        );
    }

    protected function indexColumns()
    {
        return array(
            array(
                'field' => 'title'
            ),
            array(
                'field' => 'publish_date',
                'type' => 'datetime'
            ),
            array(
                'field' => 'is_published',
                'label' => 'status',
                'option_labels' => array(
                    '0' => '<span class="label label-warning">' . $this->lang->get('draft') . '</span>',
                    '1' => '<span class="label label-success">' . $this->lang->get('published') . '</span>'
                )
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
                'url_route' => 'admin-announcement-edit',
                'link_class' => 'btn btn-primary btn-small pull-right',
                'icon' => 'fa fa-pencil',
                'label' => 'edit',
                'params' => array('id')
            ),
            'delete' => array(
                'url_route' => 'admin-announcement-delete',
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
            'Announcement.id' => array(
                'type' => 'hidden'
            ),
            'Announcement.title',
            'Announcement.body' => array(
                'type' => 'wysiwyg'
            ),
            'Announcement.is_published' => array(
                'type' => 'checkbox'
            ),
            'Announcement.publish_date',
            'Announcement.keywords',
            'Announcement.description' => array(
                'type' => 'textarea'
            ),
            'Announcement.language_id' => array(
                'type' => 'select'
            ),
            'Announcement.individual_language_only' => array(
                'type' => 'checkbox'
            ),
            'Announcement.clients_only' => array(
                'type' => 'checkbox'
            )
        );

        return $fields;
    }

    public function form($id = null)
    {
        $this->render_view = 'announcements/form.php';
        return parent::form($id);
    }

    public function getExtraData($model)
    {
        if ($model->id > 0) {
            $Carbon = \Carbon\Carbon::parse(
                $model->publish_date,
                \App::get('configs')->get('settings.localization.timezone')
            );
            $datetime = $Carbon->toDateTimeString();
        } else {
            $datetime = date('Y-m-d H:i');
        }

        $this->view->set('datetime', $datetime);

        $this->view->set('languages', Language::formattedList('id', 'name', array(), 'name', 'asc'));
    }
}
