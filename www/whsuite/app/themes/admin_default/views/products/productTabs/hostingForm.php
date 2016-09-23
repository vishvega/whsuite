<?php echo $forms->select('Product.server_group_id', $lang->get('server_group'), array('options' => $server_groups)); ?>

<div id="addonFields">
    <div class="loading hide">
        <i class="fa fa-cog fa-spin"></i> <?php echo $lang->get('loading'); ?>
    </div>
</div>

<?php echo $forms->checkbox('Product.allow_ips', $lang->get('allow_ips')); ?>
<?php echo $forms->input('Product.included_ips', $lang->get('included_ips'), array('placeholder' => '0')); ?>
<?php echo $forms->checkbox('Product.charge_bandwidth_overages', $lang->get('charge_bandwidth_overages')); ?>
<?php echo $forms->checkbox('Product.charge_disk_overages', $lang->get('charge_disk_overages')); ?>
<?php echo $forms->select('Product.domain_type', $lang->get('domain_type'), array('options' => $domain_types)); ?>
<span class="help-block"><?php echo $lang->get('domain_type_help_text'); ?></span>

<script>
    $('#productServerGroupId').change(function() {

        if (! $('#addonFields').find('.loading').length) {
            $('#addonFields').slideUp("fast");
            $('#addonFields').html('<div class="loading hide"><i class="fa fa-cog fa-spin"></i> <?php echo $lang->get('loading'); ?></div>');
            $('#addonFields').slideDown("fast");
        }

        $('#addonFields').find('.loading').removeClass('hide');

        $('#addonFields').closest('form').find('button[type=submit]').attr('disabled', 'disabled');
        $('#addonFields').closest('form').find('button[type=submit]').html('<span class="fa fa-cog fa-spin"></span> <?php echo $lang->get('loading'); ?>');

        $.get('../../../addon-fields/' + $('#productServerGroupId').val(), function(data) {

            $('#addonFields').slideUp(function() {
                $(this).html(data).slideDown();

                $('#addonFields').closest('form').find('button[type=submit]').removeAttr('disabled');
                $('#addonFields').closest('form').find('button[type=submit]').html('<?php echo $lang->get('save'); ?>');

                $('#addonFields input[type=checkbox]').bootstrapSwitch();
            });

        }, "html");
    });
</script>