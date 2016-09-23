<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php echo $view->fetch('elements/message.php'); ?>

                    <?php
                        echo $forms->open(array(
                            'role' => 'form',
                            'action' => $page_url,
                            'id' => 'currency-save'
                        ));

                        echo $forms->hidden('data.Currency.id');
                    ?>

                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">

                            <?php echo $forms->input('data.Currency.code', $lang->get('currency_code')); ?>
                            <?php echo $forms->input('data.Currency.prefix', $lang->get('prefix')); ?>
                            <?php echo $forms->input('data.Currency.suffix', $lang->get('suffix')); ?>
                            <?php echo $forms->input('data.Currency.decimals', $lang->get('decimals')); ?>
                            <?php echo $forms->input('data.Currency.decimal_point', $lang->get('decimal_point')); ?>
                            <?php echo $forms->input('data.Currency.thousand_separator', $lang->get('thousand_separator')); ?>
                            <?php echo $forms->hidden('data.Currency.conversion_rate', array('value' => '1.00')); ?>
                            <?php echo $forms->hidden('data.Currency.auto_update', array('value' => '1')); ?>

                            <fieldset>
                                <legend><?php echo $lang->get('gateways'); ?></legend>

                                <?php if ((! isset($available_gateways) || empty($available_gateways))
                                    && (! isset($selected_gateways) || empty ($selected_gateways))): ?>

                                    <p><?php echo $lang->get('no_currency_gateways_notice'); ?></p>
                                    <p>
                                        <a href="<?php echo $router->generate('admin-gateway'); ?>" class="btn btn-secondary">
                                            <?php echo $lang->get('gateway_management'); ?> <i class="fa fa-caret-right"></i>
                                        </a>
                                    </p>

                                <?php else: ?>

                                    <p><?php echo $lang->get('currency_gateway_instructions'); ?></p>
                                    <div class="row">

                                        <div class="col-md-6">
                                            <h4><?php echo $lang->get('available_gateways'); ?></h4>

                                            <ul id="available" class="connectedSortable-gateways">
                                                <?php if (isset($available_gateways) && ! empty($available_gateways)): ?>

                                                    <?php foreach ($available_gateways as $id => $gateway): ?>

                                                        <li data-gateway-id="<?php echo $id; ?>">
                                                            <?php echo $lang->get($gateway); ?>
                                                        </li>

                                                    <?php endforeach; ?>

                                                <?php endif; ?>
                                            </ul>
                                        </div>

                                        <div class="col-md-6">
                                            <h4><?php echo $lang->get('selected_gateways'); ?></h4>

                                            <ul id="selected" class="connectedSortable-gateways">
                                                <?php if (isset($selected_gateways) && ! empty($selected_gateways)): ?>

                                                    <?php foreach ($selected_gateways as $id => $gateway): ?>

                                                        <li data-gateway-id="<?php echo $gateway->id; ?>">
                                                            <?php echo $lang->get($gateway->name); ?>
                                                        </li>

                                                    <?php endforeach; ?>

                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>

                                    <?php echo $forms->hidden('data.Currency.Gateway'); ?>
                                <?php endif; ?>
                            </fieldset>


                            <div class="form-actions">
                                <?php echo $forms->submit('save', $lang->get('save')); ?>
                            </div>
                        </div>
                    </div>


                    <?php echo $forms->close(); ?>

                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php');
