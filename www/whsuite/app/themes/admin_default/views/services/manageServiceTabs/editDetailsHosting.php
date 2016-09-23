    <fieldset>
        <legend><?php echo $lang->get('hosting_service_details'); ?></legend>
        <?php echo $forms->select('Hosting.server_id', $lang->get('server'), array('options' => $server_list)); ?>
        <?php echo $forms->input('Hosting.domain', $lang->get('domain')); ?>
        <?php echo $forms->input('Hosting.nameservers', $lang->get('nameservers')); ?>
        <?php echo $forms->input('Hosting.diskspace_limit', $lang->get('diskspace_mb')); ?>
        <?php echo $forms->input('Hosting.bandwidth_limit', $lang->get('bandwidth_mb')); ?>
        <?php echo $forms->input('Hosting.username', $lang->get('username')); ?>
        <?php echo $forms->password('Hosting.change_password', $lang->get('change_password')); ?>
        <?php echo $forms->password('Hosting.change_password2', $lang->get('confirm_password')); ?>
    </fieldset>
