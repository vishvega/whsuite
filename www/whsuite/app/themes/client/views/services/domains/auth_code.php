<?php echo $view->fetch('elements/header.php'); ?>

    <div class="panel panel-primary">
        <div class="panel-heading"><?php echo $title; ?></div>
        <div class="panel-content">
            <?php
            echo $forms->open(
            array(
                'action' => '')
            ); ?>
                <?php echo $forms->input('auth_code', $lang->get('auth_code'), array('value' => $auth_code)); ?>

            <?php echo $forms->close(); ?>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>