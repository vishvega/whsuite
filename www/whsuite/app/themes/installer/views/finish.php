<?php echo $view->fetch('elements/header.php'); ?>

    <h4>Installation Complete!</h4>

    <p>WHSuite has now been set up on your server. Before you continue, you should now <strong>delete the install.php file from your server</strong>. This is located in the main web accessible part of your installation. You do not need to delete any other files.</p>
    <p>We also recommend you change the permissions of your /whsuite/app/configs directory to only allow authorized users to read and modify you config files. </p>

    <p>To proceed to log into the admin area of your new WHSuite installation, click the button below.</p>

    <p class="text-center">
        <a href="../../admin" class="btn btn-primary btn-large">Start Using WHSuite</a>
    </p>

    <h4>Useful Resources</h4>

    <ul>
        <li><a href="http://docs.whsuite.com" target="_blank">WHSuite Documentation</a></li>
        <li><a href="https://translations.whsuite.com" target="_blank">WHSuite Language Packs</a></li>
        <li><a href="https://forums.whsuite.com" target="_blank">WHSuite Community Forums</a></li>
        <li><a href="https://account.whsuite.com" target="_blank">WHSuite Cutomer Support and Billing</a></li>
    </ul>

<?php echo $view->fetch('elements/footer.php'); ?>
