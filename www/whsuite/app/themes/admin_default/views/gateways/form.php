<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php echo $view->fetch('elements/message.php'); ?>

                    <?php
                        echo $forms->open(array(
                            'role' => 'form',
                            'action' => $page_url
                        ));

                        echo $forms->hidden('data.Gateway.id');
                    ?>

                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">

                            <?php echo $forms->input('data.Gateway.name', $lang->get('name')); ?>
                            <?php echo $forms->checkbox('data.Gateway.is_active', $lang->get('active')); ?>


                            <div class="form-actions">
                                <?php echo $forms->submit('save', $lang->get('save')); ?>

                                <a href="<?php echo $router->generate('admin-currency')?>" class="btn btn-secondary pull-right"><?php echo $lang->get('currency_management'); ?> <i class="fa fa-caret-right"></i></a>
                            </div>
                        </div>
                    </div>


                    <?php echo $forms->close(); ?>

                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php');
