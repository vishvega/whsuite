<?php if(!$domain_data): ?>
    <div class="alert alert-danger"><?php echo $lang->get('domain_not_registered'); ?></div>
<?php elseif(!isset($domain_data->status) || $domain_data->status !='1' ): ?>
    <div class="alert alert-danger">
        <?php echo $lang->get('an_error_occurred'); ?>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><?php echo $lang->get('domain_details'); ?></div>
                <div class="panel-body panel-table">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <td><strong><?php echo $lang->get('expiry_date'); ?>: </strong></td>
                                <td>
                                    <?php
                                    $date = \Carbon\Carbon::parse(
                                        $domain_data->date_expires,
                                        \App::get('configs')->get('settings.timezone')
                                    );
                                    echo $date->format($date_format);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?php echo $lang->get('registrar_lock'); ?>: </strong></td>
                                <td>
                                    <?php if($domain_data->lock_status == '1'): ?>
                                        <span class="label label-success"><?php echo $lang->get('enabled'); ?></span> [<a href="<?php echo $router->generate('client-service-domain-unlock', array('service_id' => $purchase->id)); ?>"><?php echo $lang->get('disable'); ?></a>]
                                    <?php else: ?>
                                        <span class="label label-danger"><?php echo $lang->get('disabled'); ?></span> [<a href="<?php echo $router->generate('client-service-domain-lock', array('service_id' => $purchase->id)); ?>"><?php echo $lang->get('enable'); ?></a>]
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?php echo $lang->get('auth_code'); ?>: </strong></td>
                                <td>
                                    <a href="<?php echo $router->generate('client-service-domain-auth-code', array('service_id' => $purchase->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('view'); ?></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php echo $lang->get('nameservers'); ?>
                    <a href="<?php echo $router->generate('client-service-domain-nameservers', array('service_id' => $purchase->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('manage'); ?></a>
                </div>
                <div class="panel-body panel-table">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <td>
                                    <small><?php echo implode(", ", $domain_data->nameservers); ?></small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="text-center">
                <a href="<?php echo $router->generate('client-service-domain-contacts', array('service_id' => $purchase->id)); ?>" class="btn btn-primary btn-xs">
                    <?php echo $lang->get('manage_contacts'); ?>
                </a>
            </p>

        </div>
    </div>
<?php endif; ?>