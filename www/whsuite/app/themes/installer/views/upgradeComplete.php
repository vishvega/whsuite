<?php echo $view->fetch('elements/header.php'); ?>

    <h4>Upgrade Complete!</h4>
    
    <hr>

    <div class="upgrade_output">
        <div class="text-center alert alert-success">
            Your WHSuite installation has now been updated to version <?php echo $new_version; ?>
        </div>
        <p class="text-danger"><strong>You should now delete the install.php file located in your public web directory.</strong></p>
        <p>If you're using one of our translated language packs, please visit <a href="https://translations.whsuite.com">translations.whsuite.com</a>, to download the latest phrases for your languages. You can then upload these to WHSuite via the Language Management section of the admin area.</p> 
        
        <div class="form-actions text-right">
            <a href="../../admin" class="btn btn-primary">Access Admin Area</a>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>