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
            echo $assets->style('bootstrap.min.css');
            echo $assets->style('style.min.css');
            echo $assets->style('jquery-ui-1.10.4.custom.min.css');

            echo $layout_css; // echo any css added via asset->addStyle

            echo $assets->script('jquery.min.js');
            echo $assets->script('bootstrap.min.js');
            echo $assets->script('bootstrap-switch.min.js');
        ?>
    </head>
    <body>

        <div class="container">
            <p>&nbsp;</p><p>&nbsp;</p>
            <div class="col-lg-6 col-lg-offset-3">
                <p class="text-center">
                    <img src="<?php echo $assets->image('whsuite_logo.png'); ?>" alt="WHSuite">
                </p>
                <h3 class="text-center text-muted"><?php echo $title; ?></h3>
                <div class="panel">
                    <div class="panel-content">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if(isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

