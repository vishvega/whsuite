<div class="panel panel-secondary">
    <div class="panel-heading"><?php echo $lang->get('client_statistics'); ?></div>
    <div class="panel-content panel-table">
        <table class="table table-striped table-condensed">
            <tbody>
                <tr>
                    <td><strong><?php echo $lang->get('account_created'); ?>:</strong></td>
                    <td>
                        <?php
                            $Carbon = \Carbon\Carbon::parse(
                                $client->created_at,
                                $date['timezone']
                            );
                            echo $Carbon->format($date['short_date']);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php echo $lang->get('last_login'); ?>:</strong></td>
                    <td>
                        <?php
                        if ($client->last_login):
                            $Carbon = \Carbon\Carbon::parse(
                                $client->last_login,
                                $date['timezone']
                            );
                            echo $Carbon->format($date['short_datetime']);
                        else:
                            echo $lang->get('never');
                        endif;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php echo $lang->get('last_ip_address'); ?>:</strong></td>
                    <td>
                        <?php if($client->last_ip !=''): ?>
                            <?php echo $client->last_ip; ?> (<?php echo $client->last_hostname; ?>)
                        <?php else: ?>
                            <?php echo $lang->get('not_available'); ?>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <td><strong><?php echo $lang->get('currency'); ?>:</td>
                    <td><?php echo $currency->code; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $lang->get('total_products'); ?>:</td>
                    <td><?php echo $total_products; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $lang->get('active_products'); ?>:</td>
                    <td><?php echo $active_products; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
