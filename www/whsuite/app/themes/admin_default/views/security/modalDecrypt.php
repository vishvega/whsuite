<div class="modal fade" id="<?php echo (isset($modal_id)) ? $modal_id : 'securityModal'; ?>" tabindex="-1" role="dialog" aria-labelledby="securityModal" aria-hidden="true">
    <?php
        echo $forms->open(
            array(
                'action' => (isset($route_override) ? $route_override : $decryptRoute),
                'id' => (isset($modal_id) ? $modal_id . '-securityDecryptForm' : 'securityDecryptForm'),
                'class' => 'form-horizontal securityDecryptForm'
            )
        );
    ?>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $lang->get('authentication_required'); ?></h4>
            </div>
            <div class="modal-body">
                <?php
                    $password_id = (isset($modal_id)) ? $modal_id : '';
                    $password_id .= '-securityModalPasswordInput';
                ?>
                <?php if($passphraseAuth): ?>
                    <?php
                        echo $forms->password(
                            'password',
                            $lang->get('system_passphrase'),
                            array(
                                'id' => $password_id
                            )
                        );
                    ?>
                <?php else: ?>
                    <?php
                        echo $forms->password(
                            'password',
                            $lang->get('your_password'),
                            array(
                                'id' => $password_id
                            )
                        );
                    ?>
                <?php endif; ?>
                <span class="help-block"><?php echo $lang->get('all_decrypt_actions_are_logged'); ?></span>
            </div>
            <div class="modal-footer">

                <?php
                    echo $forms->submit(
                        'submit',
                        $lang->get('retrieve_data'),
                        array(
                            'class' => 'btn btn-primary pull-left',
                            'id' => (isset($modal_id) ? $modal_id . '-submit' : 'submit')
                        )
                    );
                ?>
                <?php
                    echo $forms->button(
                        'close',
                        $lang->get('close'),
                        array(
                            'data-dismiss' => 'modal',
                            'class' => 'btn',
                            'id' => (isset($modal_id) ? $modal_id . '-close' : 'close')
                        )
                    );
                ?>
            </div>
        </div>
    </div>
    <?php echo $forms->close(); ?>
</div>
