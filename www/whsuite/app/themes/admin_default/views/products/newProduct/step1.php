<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-product-add'))); ?>
                                <?php echo $forms->select('Product.product_type_id', $lang->get('product_type'), array('options' => $product_types)); ?>
                                <div class="form-actions">
                                    <?php echo $forms->submit('submit_step1', $lang->get('next_step')); ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>
