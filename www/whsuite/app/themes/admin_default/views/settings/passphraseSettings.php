<?php echo $view->fetch('elements/header.php'); ?>

<div class="content-full">
    <div class="row">
        <div class="col-lg-3 col-md-3">
            <?php $view->display('settings/sidebar.php'); ?>
        </div>

        <div class="col-lg-9 col-md-9">

            <div class="panel panel-secondary">
                <div class="panel-heading"><?php echo $title; ?></div>
                <div class="panel-content">
                    <?php echo $forms->open(array('action' => $router->generate('admin-settings-passphrase'))); ?>
                        <div class="well"><?php echo $lang->get('passphrase_info'); ?></div>

                        <?php if($passphrase_set): ?>

                            <div class="form-group">
                                <label for="current_passphrase"><?php echo $lang->get('current_passphrase'); ?></label>
                                <input type="password" name="current_passphrase" id="current_passphrase" class="form-control">
                            </div>

                        <?php endif; ?>

                        <div class="form-group">
                            <label for="passphrase"><?php echo $lang->get('passphrase'); ?></label>
                            <input type="password" name="passphrase" id="passphrase" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="confirm_passphrase"><?php echo $lang->get('confirm_passphrase'); ?></label>
                            <input type="password" name="confirm_passphrase" id="confirm_passphrase" class="form-control">
                        </div>

                        <div class="form-actions">
                            <?php echo $forms->submit('submit', $lang->get('save')); ?>
                        </div>
                    <?php echo $forms->close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
