<?php echo $view->fetch('elements/header.php'); ?>

    <h4>Step 1. Verifying your server permissions and environment.</h4>
    <p>By proceeding with the installation, you agree to the <a href="https://whsuite.com/license/" target="_blank">WHSuite License Agreement</a>.</p>
    <hr>

    <p class="alert alert-warning">WHSuite currently requires Strict Mode to be disabled in MySQL and MariaDB Installations. This will be fixed in a future release.</p>

    <?php if(isset($extensions) && !empty($extensions)): ?>
        <p class="alert alert-warning">We recommend that the following PHP extensions are installed before proceeding:</p>
        <ul>
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

        </ul>
        <hr>
    <?php endif; ?>


<?php if($missing_perms): ?>
    <h4>File Permissions</h4>
    <p>WHSuite requires write access to certain files and directories. Please make the following files and/or directories writable by your web-server user.</p>
    <hr>
    <?php foreach($failed_permissions as $file): ?>
        <p><small><?php echo $file; ?></small></p>
    <?php endforeach; ?>
    <hr>
    <p class="text-center"><a href="./" class="btn btn-primary btn-lg">Reload Page</a></p>
<?php else: ?>
    <p class="text-center">Your permissions are all correctly set!</p>
    <div class="form-actions text-right">
        <a href="./database/" class="btn btn-primary">Start Installation <i class="fa fa-caret-right"></i></a>
    </div>
<?php endif; ?>
<?php echo $view->fetch('elements/footer.php'); ?>
