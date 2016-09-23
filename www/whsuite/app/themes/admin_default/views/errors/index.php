<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-danger">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">

                            <?php echo $lang->get('404_body'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>
