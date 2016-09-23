<?php

class MenuController extends AdminController
{
    public $targets = array(
        '_self' => 'self',
        '_blank' => 'blank',
        '_parent' => 'parent',
        '_top' => 'top'
    );

    public function listing()
    {
        $title = $this->lang->get('menu_management');

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $toolbar = array(
            array(
                'url_route'=> 'admin-menu-add',
                'icon' => 'fa fa-plus',
                'label' => 'add_menu'
            ),
        );
        $this->view->set('toolbar', $toolbar);

        $menus = MenuGroup::all();

        $this->view->set('title', $title);
        $this->view->set('menus', $menus);
        $this->view->display('menus/index.php');
    }

    public function addMenu()
    {
        if (\Whsuite\Inputs\Post::Get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('MenuGroup'), MenuGroup::$rules);
            $menu_group = new MenuGroup();

            if (! $validator->fails()) {
                $menu_group->name = \Whsuite\Inputs\Post::get('MenuGroup.name');

                if ($menu_group->save()) {
                    App::get('session')->setFlash('success', $this->lang->get('menu_added'));
                    return $this->redirect('admin-menu-manage', ['id' => $menu_group->id]);
                } else {
                    \App\Libraries\Message::set($this->lang->get('error_adding_menu'), 'fail');
                }
            } else {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            }
        }

        $title = $this->lang->get('add_menu');

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('menu_management'), 'admin-menus');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $toolbar = array(
            array(
                'url_route'=> 'admin-menus',
                'icon' => 'fa fa-plus',
                'label' => 'menu_management'
            ),
        );
        $this->view->set('toolbar', $toolbar);

        $this->view->set('title', $title);
        $this->view->display('menus/addMenu.php');
    }

    public function manageMenu($id)
    {
        $menu_group = MenuGroup::find($id);
        if (empty($menu_group)) {
            return $this->redirect('admin-menus');
        }

        if (\Whsuite\Inputs\Post::Get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('MenuGroup'), MenuGroup::$rules);

            if (!$validator->fails()) {
                $menu_group->name = \Whsuite\Inputs\Post::get('MenuGroup.name');

                if ($menu_group->save()) {
                    App::get('session')->setFlash('success', $this->lang->get('menu_updated'));
                    return $this->redirect('admin-menu-manage', ['id' => $menu_group->id]);
                } else {
                    \App\Libraries\Message::set($this->lang->get('error_updating_menu'), 'fail');
                }
            } else {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            }
        }

        \Whsuite\Inputs\Post::Set('MenuGroup', $menu_group->toArray());

        $title = $this->lang->get($menu_group->name);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('menu_management'), 'admin-menus');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $links = MenuLink::where('menu_group_id', '=', $menu_group->id)
            ->where('parent_id', '=', '0')
            ->orderBy('sort', 'asc')
            ->get();
        $this->view->set('links', $links);

        $toolbar = array(
            array(
                'url_route'=> 'admin-menus',
                'icon' => 'fa fa-list',
                'label' => 'menu_management'
            ),
        );
        $this->view->set('toolbar', $toolbar);

        $link_types = array(
            '0' => $this->lang->get('internal_route'),
            '1' => $this->lang->get('full_url')
        );
        $this->view->set('link_types', $link_types);

        $this->view->set('targets', $this->targets);

        $parent_link_conditions = array(
            array(
                'column' => 'menu_group_id',
                'operator' => '=',
                'value' => $menu_group->id,
                'type' => 'and'
            ),
            array(
                'column' => 'parent_id',
                'operator' => '=',
                'value' => '0',
                'type' => 'and'
            )
        );

        $parent_links = MenuLink::formattedList('id', 'title', $parent_link_conditions, 'sort', 'asc', true);
        $this->view->set('parent_links', $parent_links);

        $this->view->set('menu', $menu_group);
        $this->view->set('title', $title);
        $this->view->display('menus/manageMenu.php');
    }

    public function addMenuLink($id)
    {
        $menu_group = MenuGroup::find($id);
        if (empty($menu_group)) {
            return $this->redirect('admin-menus');
        }

        if (\Whsuite\Inputs\Post::Get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('MenuLink'), MenuLink::$rules);

            if (!$validator->fails()) {
                if (is_null(\Whsuite\Inputs\Post::get('MenuLink.parent_id'))) {
                    $parent_id = '0';
                } else {
                    $parent_id = \Whsuite\Inputs\Post::get('MenuLink.parent_id');
                }

                $link = new MenuLink();
                $link->menu_group_id = $menu_group->id;
                $link->title = \Whsuite\Inputs\Post::get('MenuLink.title');
                $link->parent_id = $parent_id;
                $link->is_link = \Whsuite\Inputs\Post::get('MenuLink.is_link');
                $link->url = \Whsuite\Inputs\Post::get('MenuLink.url');
                $link->target = \Whsuite\Inputs\Post::get('MenuLink.target');
                $link->sort = \Whsuite\Inputs\Post::get('MenuLink.sort');
                $link->clients_only = \Whsuite\Inputs\Post::get('MenuLink.clients_only');
                $link->class = \Whsuite\Inputs\Post::get('MenuLink.class');

                // check if we are adding an internal route and whether that route exists or not
                if ($link->is_link == 0) {
                    $routes = \App::get('router')->getRoutes();

                    if (! isset($routes[$link->url])) {
                        $invalid_route = true;
                        App::get('session')->setFlash('error', $this->lang->get('invalid_route'));
                    }
                }

                // check the invalid_route function isn't set and if it is that it is false
                if (! isset($invalid_route) || ! $invalid_route) {
                    if ($link->save()) {
                        App::get('session')->setFlash('success', $this->lang->get('link_added'));
                        return $this->redirect('admin-menu-manage', ['id' => $menu_group->id]);
                    } else {
                        App::get('session')->setFlash('error', $this->lang->get('error_adding_link'));
                    }
                }

            } else {
                App::get('session')->setFlash('error', $this->lang->formatErrors($validator->messages()));
            }
        }

        return $this->redirect('admin-menu-manage', ['id' => $menu_group->id]);
    }

    public function editMenuLink($id, $link_id)
    {
        $menu_group = MenuGroup::find($id);
        $link = MenuLink::find($link_id);

        if (empty($menu_group) || empty($link) || $link->menu_group_id != $menu_group->id) {
            return $this->redirect('admin-menus');
        }

        if (\Whsuite\Inputs\Post::Get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('MenuLink'), MenuLink::$rules);

            if (! $validator->fails()) {
                $link->title = \Whsuite\Inputs\Post::get('MenuLink.title');
                $link->parent_id = \Whsuite\Inputs\Post::get('MenuLink.parent_id');
                $link->is_link = \Whsuite\Inputs\Post::get('MenuLink.is_link');
                $link->url = \Whsuite\Inputs\Post::get('MenuLink.url');
                $link->target = \Whsuite\Inputs\Post::get('MenuLink.target');
                $link->sort = \Whsuite\Inputs\Post::get('MenuLink.sort');
                $link->clients_only = \Whsuite\Inputs\Post::get('MenuLink.clients_only');
                $link->class = \Whsuite\Inputs\Post::get('MenuLink.class');

                // check if we are adding an internal route and whether that route exists or not
                if ($link->is_link == 0) {
                    $routes = \App::get('router')->getRoutes();

                    if (! isset($routes[$link->url])) {
                        $invalid_route = true;
                        \App\Libraries\Message::set($this->lang->get('invalid_route'), 'fail');
                    }
                }

                // check the invalid_route function isn't set and if it is that it is false
                if (! isset($invalid_route) || ! $invalid_route) {
                    if ($link->save()) {
                        App::get('session')->setFlash('success', $this->lang->get('link_updated'));
                        return $this->redirect('admin-menu-manage', ['id' => $menu_group->id]);
                    } else {
                        \App\Libraries\Message::set($this->lang->get('error_updating_link'), 'fail');
                    }
                }
            } else {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            }
        }

        $title = $this->lang->get($this->lang->get('edit_link').' - '.$link->title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('menu_management'), 'admin-menus');
        App::get('breadcrumbs')->add(
            $this->lang->get($menu_group->name),
            'admin-menu-manage',
            array('id' => $menu_group->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        \Whsuite\Inputs\Post::set('MenuLink', $link->toArray());

        $toolbar = array(
            array(
                'url_route'=> 'admin-menus',
                'icon' => 'fa fa-list',
                'label' => 'menu_management'
            ),
        );
        $this->view->set('toolbar', $toolbar);

        $link_types = array(
            '0' => $this->lang->get('internal_route'),
            '1' => $this->lang->get('full_url')
        );
        $this->view->set('link_types', $link_types);

        $this->view->set('targets', $this->targets);

        $parent_link_conditions = array(
            array(
                'column' => 'menu_group_id',
                'operator' => '=',
                'value' => $menu_group->id,
                'type' => 'and'
            ),
            array(
                'column' => 'parent_id',
                'operator' => '=',
                'value' => '0',
                'type' => 'and'
            ),
            array(
                'column' => 'id',
                'operator' => '!=',
                'value' => $link->id,
                'type' => 'and'
            )
        );

        $parent_links = MenuLink::formattedList('id', 'title', $parent_link_conditions, 'sort', 'asc', true);
        $this->view->set('parent_links', $parent_links);

        $this->view->set('menu', $menu_group);
        $this->view->set('link', $link);
        $this->view->set('title', $title);
        $this->view->display('menus/editMenuLink.php');
    }

    public function deleteMenuLink($id, $link_id)
    {
        $menu_group = MenuGroup::find($id);
        $link = MenuLink::find($link_id);
        if (empty($menu_group) || empty($link) || $link->menu_group_id != $menu_group->id) {
            return $this->redirect('admin-menus');
        }

        if ($link->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('link_deleted'));
            return $this->redirect('admin-menu-manage', ['id' => $menu_group->id]);
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_deleting_link'));
            return $this->redirect('admin-menu-edit-link', ['id' => $menu_group->id, 'link_id' => $link->id]);
        }
    }

    public function deleteMenu($id)
    {
        $menu_group = MenuGroup::find($id);
        if (empty($menu_group)) {
            return $this->redirect('admin-menus');
        }

        if ($menu_group->id > 2) {
            if ($menu_group->delete()) {
                App::get('session')->setFlash('success', $this->lang->get('menu_deleted'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('error_deleting_menu'));
            }
        } else {
            App::get('session')->setFlash('error', $this->lang->get('menu_cant_be_deleted'));
        }

        return $this->redirect('admin-menus');
    }
}
