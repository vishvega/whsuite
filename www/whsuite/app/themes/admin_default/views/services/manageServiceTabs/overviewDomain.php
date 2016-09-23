<div class="panel-heading"><?php echo $lang->get('domain_details'); ?> - <?php echo $domain->domain; ?></div>
<div class="panel-content">
    <?php if(!$domain_data): ?>
        <div class="alert alert-danger"><?php echo $lang->get('domain_not_registered'); ?></div>
        <hr>
        <h4><?php echo $lang->get('options'); ?></h4>
        <p>
            <a href="<?php echo $router->generate('admin-service-domain-register', array('id' => $client->id, 'service_id' => $purchase->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('register_domain'); ?></a>
            <a href="<?php echo $router->generate('admin-service-domain-transfer', array('id' => $client->id, 'service_id' => $purchase->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('transfer_domain'); ?></a>
        </p>
    <?php elseif(!isset($domain_data->status) || $domain_data->status !='1' ): ?>
        <div class="alert alert-danger">
            <?php echo $lang->get('module_returned_error'); ?><br />
            <strong><?php echo $domain_data->response->message; ?></strong>
        </div>
        <hr>
        <h4><?php echo $lang->get('options'); ?></h4>
        <p>
            <a href="<?php echo $router->generate('admin-service-domain-register', array('id' => $client->id, 'service_id' => $purchase->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('register_domain'); ?></a>
            <a href="<?php echo $router->generate('admin-service-domain-transfer', array('id' => $client->id, 'service_id' => $purchase->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('transfer_domain'); ?></a>
        </p>
    <?php else: ?>
        <div class="row">
            <div class="col-md-6">
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
                                [<a href="<?php echo $router->generate('admin-service-domain-renew', array('id' => $client->id, 'service_id' => $purchase->id)); ?>"><?php echo $lang->get('renew_domain'); ?></a>]
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('registrar_lock'); ?>: </strong></td>
                            <td>
                                <?php if($domain_data->lock_status == '1'): ?>
                                    <span class="label label-success"><?php echo $lang->get('enabled'); ?></span> [<a href="<?php echo $router->generate('admin-service-domain-unlock', array('id' => $client->id, 'service_id' => $purchase->id)); ?>"><?php echo $lang->get('disable'); ?></a>]
                                <?php else: ?>
                                    <span class="label label-danger"><?php echo $lang->get('disabled'); ?></span> [<a href="<?php echo $router->generate('admin-service-domain-lock', array('id' => $client->id, 'service_id' => $purchase->id)); ?>"><?php echo $lang->get('enable'); ?></a>]
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('auth_code'); ?>: </strong></td>
                            <td>
                                <a href="<?php echo $router->generate('admin-service-domain-auth-code', array('id' => $client->id, 'service_id' => $purchase->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('view'); ?></a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('registrar'); ?>: </strong></td>
                            <td>
                                <?php echo $registrar->name; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-6">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><strong><?php echo $lang->get('nameservers'); ?></strong> <a href="<?php echo $router->generate('admin-service-domain-nameservers', array('id' => $client->id, 'service_id' => $purchase->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('manage'); ?></a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <small><?php echo implode(", ", $domain_data->nameservers); ?></small>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-center">
                    <a href="<?php echo $router->generate('admin-service-domain-contacts', array('id' => $client->id, 'service_id' => $purchase->id)); ?>" class="btn btn-primary btn-xs">
                        <?php echo $lang->get('manage_contacts'); ?>
                    </a>
                </p>
            </div>
        </div>
    <?php endif; ?>
    </p>
</div>