<?php echo $view->fetch('elements/header.php'); ?>

<h4>Upgrade WHSuite</h4>
<p>Before you upgrade, remember to backup your files and database. Once upgraded you can not revert to a previous version.</p>
<p>As with all software upgrades, it's strongly recommended that you first perform a test upgrade on a non-production installation.</p>
<hr>

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
    <p>WHSuite requires write access to certain files and directories. Please make the following files and/or directories writable.</p>
    <hr>
    <?php foreach($failed_permissions as $file): ?>
        <p><small><?php echo $file; ?></small></p>
    <?php endforeach; ?>
    <hr>
    <p class="text-center"><a href="./" class="btn btn-primary btn-lg">Reload Page</a></p>
<?php else: ?>
    <div class="form-actions text-right">
        <a href="./upgrade" class="btn btn-primary">Upgrade WHSuite</a>
    </div>
<?php endif; ?>
<?php echo $view->fetch('elements/footer.php'); ?>