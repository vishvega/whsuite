<?php

namespace App\Libraries;

class MenuHelper
{
    public function loadMenu($id, $clients_only = false, $logged_in = false)
    {
        $menu = \MenuGroup::find($id);
        if (!empty($menu)) {

            $menu_links = array();

            $links = \MenuLink::where('menu_group_id', '=', $menu->id)
                ->where('parent_id', '=', '0')
                ->orderBy('sort', 'asc')
                ->get();

            foreach ($links as $link) {

                if ($clients_only && !$logged_in && $link->clients_only == '1') {
                    continue;
                }

                if ($link->is_link == '1')
                {
                    $url = $link->url;
                } else {
                    $url = \App::get('router')->generate($link->url);
                }

                $children = array();

                if ($link->children()->count() > 0) {
                    foreach ($link->children()->orderBy('sort', 'asc')->get() as $child) {

                        if ($clients_only && !$logged_in && $child->clients_only == '1') {
                            continue;
                        }

                        if ($child->is_link == '1')
                        {
                            $child_url = $child->url;
                        } else {
                            $child_url = \App::get('router')->generate($child->url);
                        }

                        $children[] = array(
                            'title' => \App::get('translation')->get($child->title),
                            'url' => $child_url,
                            'class' => $child->class,
                            'target' => $child->target
                        );
                    }
                }

                $menu_links[] = array(
                    'title' => \App::get('translation')->get($link->title),
                    'url' => $url,
                    'class' => $link->class,
                    'target' => $link->target,
                    'children' => $children
                );
            }
            return $menu_links;
        }

    }

}
