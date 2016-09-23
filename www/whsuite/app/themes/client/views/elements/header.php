<!DOCTYPE html>
<html lang="<?php echo $language_code; ?>" dir="<?php echo $text_direction; ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $settings['general']['sitename']; ?> Client Billing &amp; Support. Powered by WHSuite">
    <meta name="generator" content="WHSuite Billing Software">

    <?php
        $favicon = $assets->image('favicon.ico', array('render' => false));
    ?>
    <link rel='shortcut icon' type='image/x-icon' href='<?php echo $favicon; ?>' />

    <title>
        <?php
        if (isset($title)):
            echo $title.' | ';
        endif;

        echo $settings['general']['sitename'];
        ?>
    </title>

    <?php echo $assets->style('style.min.css'); ?>

    <?php echo $layout_css; // echo any css added via asset->addStyle ?>

    <?php echo $assets->script('jquery.min.js'); ?>
    <?php echo $assets->script('bootstrap.min.js'); ?>
    <?php echo $assets->script('whsuite.min.js'); ?>
</head>
<body>

<div class="container">

    <div class="row">
        <div class="header">
            <div class="col-lg-6">
                <a href="<?php echo $settings['general']['site_url']; ?>"><img src="<?php echo $assets->image('logo.png'); ?>" alt="<?php echo $settings['general']['sitename']; ?>"></a>
            </div>
            <div class="col-lg-6">
                <!-- This area can be used for any banners, links, etc -->
            </div>
        </div>
    </div>

    <nav class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-top">
                <i class="fa fa-bars"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbar-top">
            <ul class="nav navbar-nav">

                <?php foreach($client_menu as $link): ?>

                    <?php if(count($link['children']) > 0): ?>
                        <li class="dropdown">
                            <a href="<?php echo $link['url']; ?>" class="<?php echo $link['class']; ?> dropdown-toggle" data-toggle="dropdown" target="<?php echo $link['target']; ?>"><?php echo $link['title']; ?> <b class="caret"></b></a>
                    <?php else: ?>
                        <li>
                            <a href="<?php echo $link['url']; ?>" class="<?php echo $link['class']; ?>" target="<?php echo $link['target']; ?>"><?php echo $link['title']; ?></a>
                    <?php endif; ?>

                        <?php if(count($link['children']) > 0): ?>
                            <ul class="dropdown-menu">
                            <?php foreach($link['children'] as $child): ?>
                                <li><a href="<?php echo $child['url']; ?>" class="<?php echo $child['class']; ?>" target="<?php echo $child['target']; ?>"><?php echo $child['title']; ?></a></li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>

                <?php endforeach; ?>

                <?php App::get('hooks')->callListeners('client-main-navigation'); ?>
            </ul>
        </div>
    </nav>

    <div class="row">
        <div class="col-lg-12">

            <?php echo $view->fetch('elements/breadcrumbs.php'); ?>
            <?php echo $view->fetch('elements/message.php'); ?>
