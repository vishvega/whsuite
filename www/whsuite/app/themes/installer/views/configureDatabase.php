<?php echo $view->fetch('elements/header.php'); ?>
<h4>Step 3. Database Connection</h4>
<p>Enter your MySQL database connection details below. This database should already exist and should not contain any existing data.</p>
<p class="alert alert-info">We recommend that your database encoding/collation be set to <em>utf8</em>.</p>
<form action="" method="post" class="form-vertical">
    <div class="form-group">
        <label for="host">MySQL Host</label>
        <input type="text" name="host" id="host" class="form-control">
    </div>
    <div class="form-group">
        <label for="user">MySQL Username</label>
        <input type="text" name="user" id="user" class="form-control">
    </div>
    <div class="form-group">
        <label for="pass">MySQL Password</label>
        <input type="password" name="pass" id="pass" class="form-control">
    </div>
    <div class="form-group">
        <label for="name">MySQL Database Name</label>
        <input type="text" name="name" id="name" class="form-control">
    </div>

    <div class="form-actions text-right">
        <button name="submit" value="submit" class="btn btn-primary">Next Step <i class="fa fa-caret-right"></i></button>
    </div>
</form>
<?php echo $view->fetch('elements/footer.php'); ?>
