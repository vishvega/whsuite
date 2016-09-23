<?php
    echo $forms->select(
        'Domain.extension',
        $lang->get('domain_extension'),
        array(
            'options' => $domain_extensions
        )
    );
?>
<div id="addonFields"></div>

<?php if(!empty($domain_extensions)): ?>
<script>
    $('#domainExtension').change(function()
    {
        var newId = $(this).val();
        $('#addonFields').load('../../../registrar-fields/' + newId);
        $('#domainPricing').load('../../../domain-pricing/' + newId);
    }).trigger('change');
</script>
<?php endif; ?>