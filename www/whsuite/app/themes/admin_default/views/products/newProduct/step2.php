<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php echo $forms->open(array('action' => $router->generate('admin-product-add'))); ?>
                    <?php echo $forms->hidden('Product.product_type_id'); ?>
                    <div class="panel panel-primary panel-tabs ">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#basicDetails" data-toggle="tab"><?php echo $lang->get('basic_details'); ?></a></li>
                                <?php if ($product_type == 'domain'): ?>
                                    <li><a href="#typeDetails" data-toggle="tab"><?php echo $lang->get('domain_details'); ?></a></li>
                                    <li><a href="#pricingDetails" data-toggle="tab"><?php echo $lang->get('pricing_details'); ?></a></li>
                                <?php elseif ($product_type == 'hosting'): ?>
                                    <li><a href="#typeDetails" data-toggle="tab"><?php echo $lang->get('hosting_details'); ?></a></li>
                                    <li><a href="#pricingDetails" data-toggle="tab"><?php echo $lang->get('pricing_details'); ?></a></li>
                                <?php endif; ?>

                                <?php
                                if (isset($extra_tabs) && is_array($extra_tabs)):
                                    $i = 0;
                                    foreach($extra_tabs as $tab):
                                ?>
                                    <li><a href="#extraTab<?php echo $i; ?>" data-toggle="tab"><?php echo $lang->get($tab['name']); ?></a></li>
                                <?php
                                        $i++;
                                    endforeach;
                                endif;
                                ?>
                                <li><a href="#billingDetails" data-toggle="tab"><?php echo $lang->get('billing_details'); ?></a></li>

                            </ul>
                        </div>
                        <div class="tab-content panel-content">
                            <div class="tab-pane active" id="basicDetails">
                                <?php echo $view->fetch('products/productTabs/basicDetails.php'); ?>
                            </div>

                            <?php if($product_type != ''): ?>
                                <div class="tab-pane" id="typeDetails"><?php echo $extra_form; ?></div>
                            <?php endif; ?>

                            <?php if($product_type == 'domain'): ?>
                                <div class="tab-pane" id="pricingDetails">
                                    <div id="domainPricing"></div>
                                </div>
                            <?php else: ?>
                                <div class="tab-pane" id="pricingDetails">
                                    <?php echo $view->fetch('products/productTabs/productPricingDetails.php'); ?>
                                </div>
                            <?php endif; ?>

                            <?php
                            if (isset($extra_tabs) && is_array($extra_tabs)):
                                $i = 0;
                                foreach($extra_tabs as $tab):
                            ?>
                                <div class="tab-pane" id="extraTab<?php echo $i; ?>">
                                    <?php echo $view->fetch($tab['view']); ?>
                                </div>
                            <?php
                                    $i++;
                                endforeach;
                            endif;
                            ?>

                            <div class="tab-pane" id="billingDetails">
                                <?php echo $view->fetch('products/productTabs/billingDetails.php'); ?>
                            </div>

                            <div class="form-actions">
                                <?php echo $forms->submit('create_product', $lang->get('create_product')); ?>
                            </div>

                        </div>
                    </div>
                    <?php echo $forms->close(); ?>
                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>
