<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default panel-tabs">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#newclient" data-toggle="tab">New Client Registration</a></li>
                    <li><a href="#clientlogin" data-toggle="tab">Existing Client Login</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="panel-body tab-pane active" id="newclient">
                    <?php echo $forms->open(array('action' => $router->generate('client-create-account'), 'method' => 'post')); ?>
                        <?php echo $forms->hidden('redirect_to', array('value' => $router->generate('client-view-cart'))); ?>

                        <?php echo $forms->input('Client.first_name', $lang->get('first_name')); ?>
                        <?php echo $forms->input('Client.last_name', $lang->get('last_name')); ?>
                        <?php echo $forms->input('Client.company', $lang->get('company')); ?>

                        <fieldset>
                            <legend><?php echo $lang->get('contact_details'); ?></legend>
                            <?php echo $forms->input('Client.email', $lang->get('email')); ?>
                            <?php echo $forms->input('Client.address1', $lang->get('address1')); ?>
                            <?php echo $forms->input('Client.address2', $lang->get('address2')); ?>
                            <?php echo $forms->input('Client.city', $lang->get('city')); ?>
                            <?php echo $forms->input('Client.state', $lang->get('state')); ?>
                            <?php echo $forms->input('Client.postcode', $lang->get('postcode')); ?>
                            <?php echo $forms->select('Client.country', $lang->get('country'), array('options' => $country_list)); ?>
                            <?php echo $forms->input('Client.phone', $lang->get('telephone')); ?>
                        </fieldset>

                        <fieldset>
                            <legend><?php echo $lang->get('password'); ?></legend>
                            <?php echo $forms->password('Client.password', $lang->get('password')); ?>
                            <?php echo $forms->password('Client.confirm_password', $lang->get('confirm_password')); ?>
                        </fieldset>

                        <fieldset>
                            <legend><?php echo $lang->get('account_options'); ?></legend>
                            <?php echo $forms->select('Client.language_id', $lang->get('language'), array('options' => Language::formattedList())); ?>
                            <?php echo $forms->select('Client.currency_id', $lang->get('default_currency'), array('options' => Currency::formattedList('id', 'code'))); ?>
                            <?php echo $forms->checkbox('Client.html_emails', $lang->get('html_emails')); ?>
                        </fieldset>

                        <?php echo $client->customFields(); ?>

                        <div class="form-actions">
                            <?php echo $forms->submit('submit', $lang->get('create_account')); ?>
                        </div>
                    <?php echo $forms->close(); ?>
                </div>

                <div class="panel-body tab-pane" id="clientlogin">
                    <?php echo $forms->open(array('action' => $router->generate('client-login'), 'class' => 'form-vertical')); ?>
                        <?php echo $forms->hidden('redirect_to', array('value' => $router->generate('client-view-cart'))); ?>
                        <?php echo $forms->input('email', $lang->get('email')); ?>
                        <?php echo $forms->password('password', $lang->get('password')); ?>
                        <?php echo $forms->checkbox('remember', $lang->get('remember_me')); ?>

                        <div class="form-actions">
                            <?php echo $forms->submit('submit', $lang->get('login'), array('class' => 'btn btn-primary btn-lg')); ?>
                        </div>
                        <p class="text-center">
                            <a href="<?php echo $router->generate('client-forgot-password'); ?>" class="btn btn-default btn-sm"><?php echo $lang->get('reset_password'); ?></a>
                        </p>
                    <?php echo $forms->close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
