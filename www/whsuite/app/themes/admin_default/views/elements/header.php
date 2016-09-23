<!DOCTYPE html>
<html>
    <head>
        <title>
            <?php
            if (isset($title)):
                echo $title.' | ';
            endif;
            ?>
            WHSuite
        </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <?php
            $favicon = $assets->image('favicon.ico', array('render' => false));
        ?>
        <link rel='shortcut icon' type='image/x-icon' href='<?php echo $favicon; ?>' />

        <?php
            echo $assets->style('bootstrap.min.css');
            echo $assets->style('style.min.css');
            echo $assets->style('jquery-ui.min.css');

            echo $layout_css; // echo any css added via asset->addStyle

            echo $assets->script('jquery.min.js');
            echo $assets->script('jquery.cookie.min.js');
            echo $assets->script('jquery-ui.min.js');
            echo $assets->script('jquery-ui-timepicker-addon.min.js');
            echo $assets->script('bootstrap.min.js');
            echo $assets->script('bootstrap-switch.min.js');
            echo $assets->script('ckeditor/ckeditor.js');
        ?>
    </head>
    <body>
        <div class="wrapper">
            <div class="sidebutton"></div>
            <div class="sidebar">
                <div class="sidebar-wrapper">
                    <div class="logo"></div>

                    <?php echo $forms->open(array('action' => $router->generate('admin-search-results'), 'method' => 'post', 'class' => 'form-vertical')); ?>
                        <?php echo $forms->input('q', null, array('placeholder' => $lang->get('search'))); ?>
                    <?php echo $forms->close(); ?>

                    <ul class="sidenav">
                        <?php foreach($admin_menu as $link): ?>

                            <?php if(count($link['children']) > 0): ?>
                                <li class="dropdown">
                            <?php else: ?>
                                <li>
                            <?php endif; ?>

                                <a href="<?php echo $link['url']; ?>" class="<?php echo $link['class']; ?>" target="<?php echo $link['target']; ?>"><?php echo $link['title']; ?></a>

                                <?php if(count($link['children']) > 0): ?>
                                    <ul class="submenu">
                                    <?php foreach($link['children'] as $child): ?>
                                        <li><a href="<?php echo $child['url']; ?>" class="<?php echo $child['class']; ?>" target="<?php echo $child['target']; ?>"><?php echo $child['title']; ?></a></li>
                                    <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>
                </div>
            </div>
            <div class="content">
                <div class="header">
                    <a class="sidebar_toggle" id="menu-toggle"><i class="fa fa-reorder"></i></a>
                    <div class="page_title">
                        <?php if (isset($title)): ?>
                            <?php echo $title; ?>
                        <?php endif; ?>
                    </div>
                    <div class="alerts">

                        <div class="btn-group">
                            <a href="<?php echo $router->generate('client-home'); ?>" class="btn btn-header" target="_blank">
                                <i class="fa fa-home"></i>
                            </a>
                        </div>

                        <?php if($pending_order_count > 0): ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-header dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-shopping-cart"><span class="badge"><?php echo $pending_order_count; ?></span></i>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li class="dropdown-heading"><?php echo $lang->get('pending_orders'); ?></li>
                                <?php foreach($pending_orders as $order): ?>
                                <?php $client = $order->Client->first(); ?>
                                <li>
                                    <a href="<?php echo $router->generate('admin-order-view', array('id' => $order->id)) ;?>">
                                        #<?php echo $order->order_no; ?> <?php echo $client->first_name; ?> <?php echo $client->last_name; ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <div class="profile btn-group">
                            <button type="button" class="btn btn-header dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-user"></i> <?php echo $user->first_name.' '.$user->last_name; ?>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                            <li>
                                <a href="<?php echo $router->generate('admin-staff-myprofile'); ?>">
                                    <?php echo $lang->get('profile'); ?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo $router->generate('admin-logout'); ?>">
                                    <?php echo $lang->get('logout'); ?>
                                </a>
                            </li>
                        </div>
                    </div>
                </div>
                <?php if (isset($breadcrumbs) && $breadcrumbs != false): echo $breadcrumbs; endif; ?>
                <div class="clearfix"></div>
                <?php echo $view->fetch('elements/toolbar.php'); ?>
                <?php if(!isset($disable_messages) || $disable_messages != true): ?>
                    <?php echo $view->fetch('elements/message.php'); ?>
                <?php endif; ?>
