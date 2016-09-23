<?php echo $forms->hidden('data.translations.' . $language->id . '.id'); ?>
<table class="table table-striped">
    <tr>
        <td width="15%"><b><?php echo $lang->get('subject'); ?>:</b></td>
        <td width="85%"><?php echo $forms->input('data.translations.' . $language->id . '.subject', false, array('type' => 'text')); ?></td>
    </tr>
    <tr>
        <td><b><?php echo $lang->get('html'); ?>:</b></td>
        <td>
            <?php
                echo $forms->wysiwyg(
                    'data.translations.' . $language->id . '.html_body',
                    false,
                    array(
                        'type' => 'textarea',
                        'form-type' => 'form-vertical'
                    )
                );
            ?>
        </td>
    </tr>
    <tr>
        <td><b><?php echo $lang->get('plain_text'); ?>:</b></td>
        <td>
            <?php
                echo $forms->input(
                    'data.translations.' . $language->id . '.plaintext_body',
                    false,
                    array(
                        'type' => 'textarea',
                        'rows' => 10
                    )
                );
            ?>
        </td>
    </tr>
</table>