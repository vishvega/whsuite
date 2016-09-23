<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $lang->get('reset_password'); ?></div>
            <div class="panel-body">
                <?php echo $forms->open(array('action' => $router->generate('client-forgot-password'), 'method' => 'post')); ?>

                    <?php echo $forms->input('email', $lang->get('email')); ?>

                    <div class="form-actions">
                        <?php echo $forms->submit('submit', $lang->get('request_new_password')); ?>
                    </div>
                <?php echo $forms->close(); ?>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
