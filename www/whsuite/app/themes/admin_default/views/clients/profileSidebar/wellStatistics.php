<div class="row">
    <div class="col-lg-6 text-center">
        <div class="well well-stats">
        <div>
            <?php
            if ($client->status == '1'):
                echo $lang->get('active');
            elseif($client->status == '2'):
                echo $lang->get('suspended');
            elseif($client->status == '3'):
                echo $lang->get('closed');
            elseif($client->status == '0'):
                echo $lang->get('pending');
            else:
                echo $lang->get('not_available');
            endif;
            ?>
        </div>
        <small><?php echo $lang->get('account_status'); ?></small></div>
    </div>
    <div class="col-lg-6 text-center"><div class="well well-stats">
        <div><?php echo $active_products; ?></div>
        <small><?php echo $lang->get('active_products'); ?></small></div>
    </div>
</div>
