<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $lang->get('404_title'); ?></div>
            <div class="panel-body">

            <?php echo $lang->get('404_body'); ?>

            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
