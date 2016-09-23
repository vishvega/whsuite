<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">

                            <?php echo $forms->open(array('role' => 'form')); ?>
                                <?php echo $forms->input('data.ProductAddon.name', $lang->get('name')); ?>
                                <?php echo $forms->input('data.ProductAddon.addon_slug', $lang->get('addon_slug')); ?>
                                <?php echo $forms->input('data.ProductAddon.addon_value', $lang->get('addon_value')); ?>
                                <span class="help-block"><?php echo $lang->get('addon_value_help_text'); ?></span>
                                <?php echo $forms->textarea('data.ProductAddon.description', $lang->get('description')); ?>
                                <?php echo $forms->checkbox('data.ProductAddon.is_free', $lang->get('make_product_addon_free')); ?>
                                <fieldset>
                                    <legend><?php echo $lang->get('products'); ?></legend>
                                    <div class="row checklist">
                                    <?php foreach ($products as $product): ?>
                                        <div class="col-3">
                                    <?php echo $forms->checkbox('data.Products.'.$product->id, $product->name);?>
                                        </div>
                                    <?php endforeach; ?>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend><?php echo $lang->get('pricing'); ?></legend>

                                    <table class="table table-striped">
                                    <?php foreach($currencies as $currency): ?>
                                        <thead>
                                            <tr>
                                                <th><?php echo $currency->code; ?></th>
                                                <th><?php echo $lang->get('price'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($billing_periods as $period): ?>
                                            <tr>
                                                <td><?php echo $lang->get($period->name); ?></td>
                                                <td><?php echo $forms->input('data.ProductAddonPricing.'.$period->id.'.'.$currency->id, null, array('placeholder' => '0.00')); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    <?php endforeach; ?>
                                    </table>

                                </fieldset>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $view->fetch('elements/footer.php');
