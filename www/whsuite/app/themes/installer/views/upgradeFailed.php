<?php echo $view->fetch('elements/header.php'); ?>

    <h4>Upgrade Failed!</h4>
    
    <hr>

    <div class="upgrade_output">
        <div class="text-center alert alert-danger">
            An error occurred when attempting to upgrade WHSuite to version <?php echo $new_version; ?>
        </div>
        
        <p>Please check your server permissions are correct, and that your server meets the minimum system requirements.</p>
        <p>For detailed information on updating WHSuite please visit our <a href="http://docs.whsuite.com">documentation site</a>.</p> 
        
        <div class="form-actions text-left">
            <a href="../../admin" class="btn btn-primary">Try Again</a>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>