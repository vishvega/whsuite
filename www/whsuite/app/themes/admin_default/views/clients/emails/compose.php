<?php echo $view->fetch('elements/header.php'); ?>
    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="panel panel-secondary">
                        <div class="panel-heading">
                            <?php echo $lang->get('available_variables'); ?>
                        </div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('variable'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('value'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{client.first_name}}</td>
                                        <td><?php echo $client->first_name; ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{client.last_name}}</td>
                                        <td><?php echo $client->last_name; ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{client.company}}</td>
                                        <td><?php echo $client->company; ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{client.address1}}</td>
                                        <td><?php echo $client->address1; ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{client.address2}}</td>
                                        <td><?php echo $client->address2; ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{client.city}}</td>
                                        <td><?php echo $client->city; ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{client.state}}</td>
                                        <td><?php echo $client->state; ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{client.postcode}}</td>
                                        <td><?php echo $client->postcode; ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{client.country}}</td>
                                        <td><?php echo $client->country; ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{client.phone}}</td>
                                        <td><?php echo $client->phone; ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{settings.general.sitename}}</td>
                                        <td><?php echo App::get('configs')->get('settings.general.sitename'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{{settings.mail.email_signature_html}}}</td>
                                        <td><?php echo App::get('configs')->get('settings.mail.email_signature_html'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>{{settings.general.site_url}}</td>
                                        <td><?php echo App::get('configs')->get('settings.general.site_url'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-content"><?php echo $lang->get('mustache_description'); ?></div>
                    </div>

                </div>
                <div class="col-lg-8">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">
                            <?php echo $forms->open(array('method' => 'post', 'class' => '', 'id' => 'composeEmailForm')); ?>
                                <table class="table table-striped">
                                    <tr>
                                        <td width="15%"><b><?php echo $lang->get('to'); ?>:</b></td>
                                        <td width="85%"><?php echo $client->first_name.' '.$client->last_name; ?> &lt;<?php echo $client->email; ?>&gt;</td>
                                    </tr>
                                    <tr>
                                        <td><b><?php echo $lang->get('cc'); ?>:</b></td>
                                        <td><?php echo $forms->input('cc', false, array('type' => 'text')); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b><?php echo $lang->get('bcc'); ?>:</b></td>
                                        <td><?php echo $forms->input('bcc', false, array('type' => 'text')); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b><?php echo $lang->get('subject'); ?>:</b></td>
                                        <td><?php echo $forms->input('subject', false, array('type' => 'text')); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <?php echo $forms->wysiwyg('email_body', false, array('rows' => '10', 'placeholder' => $lang->get('start_typing_email_here'), 'form-type' => 'form-vertical')); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b><?php echo $lang->get('send_as'); ?>:</b></td>
                                        <td>
                                            <?php
                                            if ($client->html_emails == '1'):
                                                echo $lang->get('html');
                                            else:
                                                echo $lang->get('plain_text');
                                            endif;
                                            ?>
                                        </td>
                                    </tr>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="text-right">
                                                <div class="row">
                                                    <div class="col-lg-6 text-left">
                                                        <a href="#" id="plaintextPreview" class="btn btn-secondary" data-preview-url="<?php echo $router->generate('admin-clientemail-plaintext-preview', array('id' => $client->id)); ?>">
                                                            <?php echo $lang->get('plaintext_preview'); ?>
                                                        </a>
                                                    </div>
                                                    <div class="col-lg-6 text-right">
                                                        <?php echo $forms->submit('submit', $lang->get('send_email')); ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="emailModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $lang->get('view_email'); ?></h4>
                </div>
                <div class="modal-body">
                    <p><?php echo $lang->get('loading'); ?></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal"><?php echo $lang->get('close'); ?></button>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php');
