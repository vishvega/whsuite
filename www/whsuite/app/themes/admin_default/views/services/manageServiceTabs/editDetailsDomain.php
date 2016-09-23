    <fieldset>
        <legend><?php echo $lang->get('domain_details'); ?></legend>
        <?php echo $forms->select('Domain.registrar_id', $lang->get('registrar'), array('options' => $registrars)); ?>
    </fieldset>
