<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <div class="tabbable tabs-left row">
                    <div class="col-md-2">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#overview" data-toggle="tab"><?php echo $lang->get('overview'); ?></a></li>
                            <?php if ($manage_route): ?>
                            <li><a href="#manage" data-toggle="tab"><?php echo $lang->get('manage_service'); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col-md-10">
                        <div class="tab-content">
                            <div class="tab-pane active" id="overview">
                                <?php if($hosting): ?>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <b><?php echo $lang->get('ip_addresses'); ?>:</b> <?php echo $service_ip; ?><br>
                                        <b><?php echo $lang->get('hostname'); ?>:</b> <?php echo $hosting->domain; ?>
                                    </div>
                                    <div class="col-lg-6">
                                        <b><?php echo $lang->get('product'); ?>:</b> <?php echo $product->name; ?> (<?php echo $product_group->name; ?>)<br>
                                        <b>Service Status:</b>
                                        <?php if($purchase->status == '1'): ?>
                                            <span class="text-success"><?php echo $lang->get('active'); ?></span>
                                        <?php elseif($purchase->status == '2'): ?>
                                            <span class="text-warning"><?php echo $lang->get('suspended'); ?></span>
                                        <?php elseif($purchase->status == '3'): ?>
                                            <span class="text-danger"><?php echo $lang->get('terminated'); ?></span>
                                        <?php else: ?>
                                            <span class="text-default"><?php echo $lang->get('pending'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <hr>
                                <?php endif; ?>

                                <div class="row">
                                    <?php if($hosting || $domain): ?>
                                    <div class="col-md-6">
                                    <?php else: ?>
                                    <div class="col-xs-12">
                                    <?php endif; ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading"><?php echo $lang->get('overview'); ?></div>
                                            <div class="panel-body panel-table">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <td><?php echo $lang->get('date_created'); ?>:</td>
                                                            <td>
                                                                <?php
                                                                    $Carbon = \Carbon\Carbon::parse(
                                                                        $purchase->created_at,
                                                                        $date['timezone']
                                                                    );
                                                                    echo $Carbon->format($date['short_date']);
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $lang->get('date_renewal'); ?>:</td>
                                                            <td>
                                                                <?php
                                                                    $Carbon = \Carbon\Carbon::parse(
                                                                        $purchase->next_renewal,
                                                                        $date['timezone']
                                                                    );
                                                                    echo $Carbon->format($date['short_date']);
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $lang->get('first_payment'); ?>:</td>
                                                            <td><?php echo App::get('money')->format($purchase->first_payment, $purchase->currency_id, false, true); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $lang->get('recurring_payment'); ?>:</td>
                                                            <td><?php echo App::get('money')->format($purchase->recurring_payment, $purchase->currency_id, false, true); ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if($hosting || $domain): ?>
                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading"><?php echo $lang->get('account_details'); ?></div>
                                            <div class="panel-body panel-table">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <?php if($hosting): ?>
                                                        <tr>
                                                            <td><?php echo $lang->get('diskspace_mb'); ?>:</td>
                                                            <td><?php echo $hosting->diskspace_limit; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $lang->get('bandwidth_mb'); ?>:</td>
                                                            <td><?php echo $hosting->bandwidth_limit; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $lang->get('server'); ?>:</td>
                                                            <td><?php echo $server->name; ?> (<?php echo $server->location; ?>)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $lang->get('username'); ?>:</td>
                                                            <td><?php echo $hosting->username; ?></td>
                                                        </tr>
                                                        <?php elseif($domain): ?>
                                                        <tr>
                                                            <td><?php echo $lang->get('domain'); ?>:</td>
                                                            <td><?php echo $domain->domain; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $lang->get('nameservers'); ?>:</td>
                                                            <td><?php echo $domain->nameservers; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo $lang->get('registrar_lock'); ?>:</td>
                                                            <td>
                                                                <?php if($domain->registrar_lock == '1'): ?>
                                                                    <span class="text-success"><?php echo $lang->get('enabled'); ?></span>
                                                                <?php else: ?>
                                                                    <span class="text-danger"><?php echo $lang->get('disabled'); ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                            <div class="tab-pane" id="manage">
                                <?php if(isset($manage_route)): ?>
                                    <p class="loading"><?php echo $lang->get('loading'); ?></p>
                                <?php else: ?>
                                    <p class="alert alert-danger"><?php echo $lang->get('no_management_options_available'); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$( "#manage" ).load( "<?php echo $manage_route; ?>", function() {

});
</script>

<?php echo $view->fetch('elements/footer.php'); ?>
