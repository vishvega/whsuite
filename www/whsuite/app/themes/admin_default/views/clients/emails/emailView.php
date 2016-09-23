<table class="table table-striped nomargin">
    <tbody>
        <tr>
            <td width="20%"><b><?php echo $lang->get('date_sent'); ?>:</b></td>
            <td width="80%">
                <?php
                    $Carbon = \Carbon\Carbon::parse(
                        $email->created_at,
                        $date['timezone']
                    );
                    echo $Carbon->format($date['full_datetime']);
                ?>
            </td>
        </tr>
        <tr>
            <td><b><?php echo $lang->get('to'); ?>:</b></td>
            <td>
                <?php echo $client->first_name.' '.$client->last_name; ?> &lt;<?php echo $email->to; ?>&gt;
            </td>
        </tr>
        <?php if($email->cc !=''): ?>
        <tr>
            <td><b><?php echo $lang->get('cc'); ?>:</b></td>
            <td><?php echo $email->cc; ?></td>
        </tr>
        <?php endif; ?>
        <?php if($email->bcc !=''): ?>
        <tr>
            <td><b><?php echo $lang->get('bcc'); ?>:</b></td>
            <td><?php echo $email->bcc; ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td><b><?php echo $lang->get('subject'); ?>:</b></td>
            <td><?php echo $email->subject; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="min-height: 300px; max-height: 400px; overflow:auto">
                <?php if (htmlspecialchars_decode($email->body) !=strip_tags($email->body)): ?>
                    <iframe
                        src="<?php echo $router->generate('admin-clientemail-view-body', array('id' => $client->id, 'email_id' => $email->id)); ?>"
                        width="100%" height="100%" frameBorder="0" seamless="seamless"></iframe>
                    <?php
                else:
                    echo nl2br($email->body);
                endif;
                ?>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">
                <div class="row">
                    <div class="col-lg-6 text-center">
                        <a href="<?php echo $router->generate('admin-clientemail-resend', array('id' => $client->id, 'email_id' => $email->id)); ?>" class="btn btn-primary">
                            <?php echo $lang->get('resend_email'); ?>
                        </a>
                    </div>
                    <div class="col-lg-6 text-center">
                        <a href="<?php echo $router->generate('admin-clientemail-delete', array('id' => $client->id, 'email_id' => $email->id)); ?>" class="btn btn-danger" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>')">
                            <?php echo $lang->get('delete_email'); ?>
                        </a>
                    </div>
                </div>
            </td>
        </tr>
    </tfoot>
</table>
