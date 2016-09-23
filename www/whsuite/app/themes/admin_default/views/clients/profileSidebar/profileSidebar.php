<div class="panel panel-primary">
    <div class="panel-heading"><?php echo $lang->get('manage_client'); ?></div>
    <div class="panel-content">
        <ul class="nav nav-pills nav-stacked">
            <li class="active"><a href="#overview" role="tab" data-toggle="tab"><?php echo $lang->get('overview'); ?></a></li>
            <li><a href="#services" role="tab" data-toggle="tab"><?php echo $lang->get('services'); ?></a></li>
            <li><a href="#invoices" role="tab" data-toggle="tab"><?php echo $lang->get('invoices'); ?></a></li>
            <li><a href="#transactions" role="tab" data-toggle="tab"><?php echo $lang->get('transactions'); ?></a></li>

            <?php if ($store_credit_cards == 1 || $store_ach == 1): ?>
                <li><a href="#ccachaccounts" role="tab" data-toggle="tab"><?php echo $lang->get('cc_ach_accounts'); ?></a></li>
            <?php endif; ?>

            <li><a href="#emails" role="tab" data-toggle="tab"><?php echo $lang->get('emails'); ?></a></li>
            <li><a href="#profile" role="tab" data-toggle="tab"><?php echo $lang->get('profile'); ?></a></li>
            <li><a href="#notes" role="tab" data-toggle="tab"><?php echo $lang->get('notes'); ?></a></li>
        </ul>
    </div>
</div>

<div class="panel panel-primary hidden-sm hidden-xs">
    <div class="panel-heading"><?php echo $lang->get('shortcuts'); ?></div>
    <div class="panel-content">
        <ul class="nav nav-pills nav-stacked">
            <?php if($client->activated == '0'): ?>
                <li>
                    <a href="<?php echo $router->generate('admin-client-activate', array('id' => $client->id)); ?>">
                        <?php echo $lang->get('activate'); ?>
                    </a>
                </li>
            <?php else: ?>
                <li><a href="<?php echo $router->generate('admin-client-login', array('id' => $client->id)); ?>" target="_blank"><?php echo $lang->get('login_as_client'); ?></a></li>
                <li><a href="<?php echo $router->generate('admin-client-email-new-password', array('id' => $client->id)); ?>"><?php echo $lang->get('email_new_password'); ?></a></li>
            <?php endif; ?>
            <li><a href="<?php echo $router->generate('admin-clientemail-add', array('id' => $client->id)); ?>"><?php echo $lang->get('send_email'); ?></a></li>
            <li><a href="<?php echo $router->generate('admin-client-new-transaction', array('id' => $client->id)); ?>"><?php echo $lang->get('add_funds'); ?></a></li>
        </ul>
    </div>
</div>