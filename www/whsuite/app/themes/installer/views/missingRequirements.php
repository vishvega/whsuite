<?php echo $view->fetch('elements/header.php'); ?>

    <h4>Please check your server configuration!</h4>

    <hr>

    <div class="upgrade_output">
        <div class="text-center alert alert-danger">
            The following issues must be rectified before you can install WHSuite on your server.
        </div></p>
        <ul>
        <?php if(!empty($extensions)): ?>
            <?php foreach($extensions as $extension): ?>

                <?php if(isset($extension['message']) && $extension['message'] !=''): ?>
                    <li><?php echo $extension['message']; ?></li>
                <?php else: ?>

                    <?php if($extension['type'] == 'required'): ?>

                        <li>The <strong><?php echo $extension['extension']; ?></strong> extension must be installed to use WHSuite.</li>

                    <?php elseif($extension['type'] == 'recommended'): ?>

                        <li>It's recommended that the <?php echo $extension['extension']; ?> is installed on your system before you proceed.</li>

                    <?php endif; ?>

                <?php endif; ?>
            <?php endforeach; ?>

        <?php endif; ?>

        <?php if(isset($required_php)): ?>

            <li>Your installed PHP version is <strong><?php echo $installed_php; ?></strong>, however WHSuite requires PHP <strong><?php echo $required_php; ?></strong> or above.</li>
        <?php elseif(isset($recommended_php)): ?>
            <li>Your installed PHP version is <strong><?php echo $installed_php; ?></strong>, however we <strong>recommend upgrading</strong> to at least PHP version <strong><?php echo $recommended_php; ?></strong> as WHSuite may no longer support PHP version <?php echo $installed_php; ?> in a future update.</li>
        <?php endif; ?>


        </ul>
        <div class="form-actions text-right">
            <a href="./" class="btn btn-primary">Reload Page</a>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>
