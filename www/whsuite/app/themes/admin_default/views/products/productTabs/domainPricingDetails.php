<table class="table table-striped" id="pricingTable">
<?php foreach($currencies as $currency): ?>
    <thead>
        <tr>
            <th><?php echo $currency->code; ?></th>
            <th><?php echo $lang->get('registration_price_year'); ?></th>
            <th><?php echo $lang->get('renewal_price_year'); ?></th>
            <th><?php echo $lang->get('transfer_price_year'); ?></th>
            <th><?php echo $lang->get('restore_price_year'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php for($i = $extension->min_years;$i<=$extension->max_years;$i++): ?>
        <tr>
            <td>
                <strong>
                    <?php
                    if($i == 1):
                        echo $i.' '.$lang->get('year');
                    else:
                        echo $i.' '.$lang->get('years');
                    endif;
                    ?>
                </strong>
            </td>
            <td><?php echo $forms->input('Pricing.'.$i.'.'.$currency->id.'.registration', null, array('placeholder' => '0.00')); ?></td>
            <td><?php echo $forms->input('Pricing.'.$i.'.'.$currency->id.'.renewal', null, array('placeholder' => '0.00')); ?></td>
            <td><?php echo $forms->input('Pricing.'.$i.'.'.$currency->id.'.transfer', null, array('placeholder' => '0.00')); ?></td>
            <td><?php echo $forms->input('Pricing.'.$i.'.'.$currency->id.'.restore', null, array('placeholder' => '0.00')); ?></td>
        </tr>
        <?php endfor; ?>
    </tbody>
    <?php endforeach; ?>
</table>

