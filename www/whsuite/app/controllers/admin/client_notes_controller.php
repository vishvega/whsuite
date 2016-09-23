<?php

/**
 * Clients Notes Admin Controller
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class ClientNotesController extends AdminController
{
    protected function indexToolbar()
    {
        $route = \App::get('dispatcher')->getRoute();
        return array(
            array(
                'url_route' => 'admin-client-profile',
                'link_class' => '',
                'icon' => 'fa fa-list-ul',
                'label' => 'manage_client',
                'route_params' => array(
                    'id' => $route->values['id']
                )
            ),
            array(
                'url_route' => 'admin-clientnote-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'new_note',
                'route_params' => array(
                    'id' => $route->values['id']
                )
            )
        );
    }

    public function listNotes($id, $page = 1)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        $title = $this->lang->get('client_notes');
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

        $conditions = array(
            array(
                'type' => 'and',
                'column' => 'client_id',
                'operator' => '=',
                'value' => $client->id
            )
        );

        $notes = ClientNote::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'updated_at', 'desc', 'admin-client-notes-paging', array('id' => $client->id));

        $this->view->set(
            array(
                'notes' => $notes,
                'client' => $client,
                'toolbar' => $this->indexToolbar()
            )
        );
        $this->view->display('client_notes/listNotes.php');
    }

    public function notes_form($id, $note_id = null)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        if ($note_id) {
            $note = ClientNote::find($note_id);

            if ($client->id != $note->client_id) {
                return $this->redirect('admin-client');
            }
        }

        if (\Whsuite\Inputs\Post::get()) {
            $post_data = \Whsuite\Inputs\Post::get('Note');

            $validator = $this->validator->make($post_data, ClientNote::$rules);
            if (!$validator->fails()) {
                if (!isset($note)) {
                    $note = new ClientNote();
                }

                $note->client_id = $client->id;
                $note->note = $post_data['note'];

                if ($note->save()) {
                    App::get('session')->setFlash('success', $this->lang->get('scaffolding_save_success'));
                    return $this->redirect('admin-clientnote', ['id' => $client->id]);
                } else {
                    \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
                }

            } else {
                \App\Libraries\Message::set($this->lang->get('scaffolding_save_error'), 'fail');
            }
        }

        if ($note_id) {
            $title = $this->lang->get('edit_note');
            \Whsuite\Inputs\Post::set('note', $note);
        } else {
            $title = $this->lang->get('add_note');
            $note = new ClientNote();
        }

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add(
            $this->lang->get('client_notes'),
            'admin-clientnote',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

        $this->view->set(
            array(
                'note' => $note,
                'client' => $client,
                'toolbar' => $this->indexToolbar()
            )
        );
        $this->view->display('client_notes/form.php');
    }

    public function notes_delete($id, $note_id)
    {
        $client = Client::find($id);
        $note = ClientNote::find($note_id);
        if (empty($client) || empty($note) || $client->id != $note->client_id) {
            return $this->redirect('admin-client');
        }

        if ($note->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('scaffolding_delete_success'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('scaffolding_delete_error'));
        }
        return $this->redirect('admin-clientnote', ['id' => $client->id]);
    }
}
