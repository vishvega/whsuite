<?php echo $view->fetch('elements/header.php'); ?>

    <?php if (isset($shortcuts) && ! empty($shortcuts)): ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="shortcuts">

                        <?php foreach ($shortcuts as $shortcut): ?>

                            <?php
                                $shortcut_name = (isset($shortcut->addon_id) && $shortcut->addon_id > 0) ? $shortcut->addon_id . '_' : '';
                                $shortcut_name .= $shortcut->unique_name;
                            ?>

                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <a href="<?php echo $router->generate($shortcut->route); ?>" class="btn btn-dashbox" id="<?php echo $shortcut_name; ?>">
                                    <?php if (isset($shortcut->label_route) && ! empty($shortcut->label_route)): ?>
                                        <div class="shortcut-label" data-label-route="<?php echo $router->generate($shortcut->label_route); ?>"></div>
                                    <?php endif; ?>

                                    <?php if (isset($shortcut->icon_class) && ! empty($shortcut->icon_class)): ?>
                                        <i class="<?php echo $shortcut->icon_class; ?>"></i>
                                    <?php endif; ?>

                                    <?php echo $lang->get($shortcut->name); ?>
                                </a>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <div class="col-lg-12">
        <div class="row">

            <?php if (isset($widgets) && ! empty($widgets)): ?>

                <?php foreach ($widgets as $widget): ?>

                    <?php
                        $widget_name = (isset($widget->addon_id) && $widget->addon_id > 0) ? $widget->addon_id . '_' : '';
                        $widget_name .= $widget->unique_name;
                    ?>

                    <div class="col-md-2 dashboard-widget" data-widget-url="<?php echo $router->generate($widget->route); ?>" id="<?php echo $widget_name; ?>">

                        <p><i class="fa fa-spinner fa-spin"></i> <?php echo $lang->get('loading_widget'); ?></p>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </div>

<?php echo $view->fetch('elements/footer.php');
