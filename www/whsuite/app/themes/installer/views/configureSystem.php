<?php echo $view->fetch('elements/header.php'); ?>

    <form action="" method="post" class="form-vertical">
        <h4>Step 4. System Configuration</h4>
        <div class="form-group">
            <label for="site_url">WHSuite Installation URL</label>
            <input type="text" name="site_url" id="site_url" class="form-control" placeholder="e.g http://example.com" value="<?php echo $siteUrlPlaceholder; ?>">
        </div>
        <div class="form-group">
            <label for="site_name">Site Name (This will be displayed in the header and footer of your WHSuite installation)</label>
            <input type="text" name="site_name" id="site_name" class="form-control" value="New WHSuite Installation">
        </div>
        <hr>

        <h4>Create your first admin user account</h4>

        <div class="form-group">
            <label for="admin_email">Admin Email:</label>
            <input type="text" name="admin_email" id="admin_email" class="form-control" placeholder="e.g admin@example.com">
        </div>

        <div class="form-group">
            <label for="admin_password">Admin Password:</label>
            <input type="password" name="admin_password" id="admin_password" class="form-control">
        </div>

        <div class="form-actions text-right">
            <button name="submit" value="submit" class="btn btn-primary">Complete Installation <i class="fa fa-caret-right"></i></button>
        </div>
    </form>
<?php echo $view->fetch('elements/footer.php'); ?>
