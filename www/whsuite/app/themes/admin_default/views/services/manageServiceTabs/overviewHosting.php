<div class="row">
    <div class="col-lg-7">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('product_details'); ?></div>
            <div class="panel-content panel-table">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td><strong><?php echo $lang->get('domain'); ?>:</strong></td>
                            <td><?php echo $hosting->domain; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('date_created'); ?>:</strong></td>
                            <td>
                                <?php
                                    $Carbon = \Carbon\Carbon::parse(
                                        $hosting->created_at,
                                        $date['timezone']
                                    );
                                    echo $Carbon->format($date['short_date']);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('date_renewal'); ?>:</strong></td>
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
                            <td><strong><?php echo $lang->get('first_payment'); ?>:</strong></td>
                            <td><?php echo App::get('money')->format($purchase->first_payment, $currency->code); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('recurring_payment'); ?>:</strong></td>
                            <td><?php echo App::get('money')->format($purchase->recurring_payment, $currency->code); ?></td>
                        </tr>
                        <?php if(isset($promotion)): ?>
                            <tr>
                                <td><strong><?php echo $lang->get('promotion_code'); ?>:</strong></td>
                                <td>
                                    <?php echo $promotion->code; ?>
                                    <?php if($promotion->is_recurring == '1'): ?>
                                        (<?php echo $lang->get('recurring_discount'); ?>)
                                    <?php else: ?>
                                        (<?php echo $lang->get('one_time_discount'); ?>)
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('account_usage'); ?></div>
            <div class="panel-content panel-table">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td><strong><?php echo $lang->get('diskspace_mb'); ?>:</strong></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" aria-valuetransitiongoal="<?php echo $hosting->diskspace_usage; ?>"
                                     aria-valuemin="0" aria-valuemax="<?php echo $hosting->diskspace_limit; ?>">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('bandwidth_mb'); ?>:</strong></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" aria-valuetransitiongoal="<?php echo $hosting->bandwidth_usage; ?>"
                                     aria-valuemin="0" aria-valuemax="<?php echo $hosting->bandwidth_limit; ?>">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('server'); ?>:</strong></td>
                            <td><?php echo (! empty($server)) ? $server->name : ''; ?> (<?php echo ( ! empty($server_module)) ? $server_module->name : ''; ?>)</td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('username'); ?>:</strong></td>
                            <td><?php echo $hosting->username; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('password'); ?>:</strong></td>
                            <td>
                                [<?php echo $lang->get('encrypted'); ?>]
                                <a href="#securityModal" class="showSecurityModal">
                                    <?php echo $lang->get('view'); ?>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('security/modalDecrypt.php'); ?>
