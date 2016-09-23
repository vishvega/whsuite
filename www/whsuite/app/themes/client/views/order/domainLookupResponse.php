<?php if(!empty($domains)): ?>
    <hr>
    <div class="row">
        <div class="col-md-3"><strong><?php echo $lang->get('domain'); ?></strong></div>
        <div class="col-md-3"><strong><?php echo $lang->get('status'); ?></strong></div>
    </div>
    <br>
    <?php foreach($domains as $domain): ?>
        <div class="row">
            <?php echo $forms->open(array('action' => $router->generate('client-order'), 'method' => 'post', 'class' => '')); ?>
                <?php echo $forms->hidden('product_id', array('value' => $domain['product']->id)); ?>
                <?php echo $forms->hidden('extension_id', array('value' => $domain['extension']->id)); ?>
                <?php echo $forms->hidden('domain', array('value' => $domain['domain'])); ?>

                <div class="col-md-3"><?php echo $domain['domain']; ?></div>
                <div class="col-md-3">
                    <?php if($domain['availability'] == 'available'): ?>
                        <span class="text-success"><?php echo $lang->get('available'); ?></span>
                    <?php elseif($domain['availability'] == 'registered'): ?>
                        <span class="text-danger"><?php echo $lang->get('registered'); ?></span>
                    <?php else: ?>
                        <span class="text-muted"><?php echo $lang->get('unknown'); ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-md-3 text-center">
                    <?php echo $forms->select('billing_period', '', array('options' => $domain['pricing'])); ?>
                </div>
                <div class="col-md-3 text-right">
                    <?php if($domain['availability'] == 'available'): ?>
                        <?php echo $forms->submit('register', $lang->get('register')); ?>
                    <?php elseif($domain['availability'] == 'registered'): ?>
                        <?php echo $forms->submit('transfer', $lang->get('transfer')); ?>
                    <?php else: ?>
                        <?php echo $forms->submit('register', $lang->get('register'), array('class' => 'disabled', 'disabled' => 'disabled')); ?>
                    <?php endif; ?>
                </div>
            <?php echo $forms->close(); ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
