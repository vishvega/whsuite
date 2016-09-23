<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">

                    <?php if (isset($model_object->available_tags) && ! empty($model_object->available_tags)): ?>
                        <div class="panel panel-secondary">
                            <div class="panel-heading">
                                <?php echo $lang->get('available_variables'); ?>
                            </div>
                            <div class="panel-content panel-table">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo $lang->get('variable'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $vars = explode(',', $model_object->available_tags); ?>

                                        <?php foreach ($vars as $var): ?>
                                            <tr>
                                                <td><?php echo $var; ?></td>
                                            </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="panel">
                        <div class="panel-content"><?php echo $lang->get('mustache_description'); ?></div>
                    </div>

                    <?php if($model_object->id > 0 && $model_object->is_system == '0'): ?>
                    <div class="panel">
                        <div class="panel-content text-center">
                            <a href="<?php echo App::get('router')->generate('admin-emailtemplate-delete', array('id' => $model_object->id)); ?>" class="btn btn-danger btn-large"><?php echo $lang->get('delete_email_template'); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-8">
                    <?php
                        echo $forms->open(array(
                            'role' => 'form',
                            'action' => $page_url,
                            'class' => ''
                        ));

                        echo $forms->hidden('data.EmailTemplate.id');
                    ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">

                            <table class="table table-striped">
                                <tr>
                                    <td width="15%"><b><?php echo $lang->get('name'); ?>:</b></td>
                                    <td width="85%">
                                    <?php if($model_object->id > 0 && $model_object->is_system == '1'): ?>
                                        <?php echo $model_object->name; ?>
                                        <?php echo $forms->hidden('data.EmailTemplate.name'); ?>
                                    <?php else: ?>
                                        <?php echo $forms->input('data.EmailTemplate.name', false, array('type' => 'text')); ?>
                                    <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%"><b><?php echo $lang->get('slug'); ?>:</b></td>
                                    <td width="85%">
                                    <?php if($model_object->id > 0 && $model_object->is_system == '1'): ?>
                                        <?php echo $model_object->slug; ?>
                                        <?php echo $forms->hidden('data.EmailTemplate.slug'); ?>
                                    <?php else: ?>
                                        <?php echo $forms->input('data.EmailTemplate.slug', false, array('type' => 'text')); ?>
                                    <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b><?php echo $lang->get('cc'); ?>:</b></td>
                                    <td><?php echo $forms->input('data.EmailTemplate.cc', false, array('type' => 'text')); ?></td>
                                </tr>
                                <tr>
                                    <td><b><?php echo $lang->get('bcc'); ?>:</b></td>
                                    <td><?php echo $forms->input('data.EmailTemplate.bcc', false, array('type' => 'text')); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="panel panel-primary panel-tabs">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">

                                <?php if (isset($languages) && ! empty($languages)): ?>

                                    <?php $active = ' class="active"'; ?>
                                    <?php foreach ($languages as $language): ?>

                                        <li<?php echo $active; ?>>
                                            <a href="#<?php echo $language->id . '-' . $language->slug; ?>" data-toggle="tab">
                                                <?php echo $language->name. ' (' . $language->slug . ')'; ?>
                                            </a>
                                        </li>

                                        <?php $active = ''; ?>
                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </ul>
                        </div>

                        <div class="tab-content">

                            <?php if (isset($languages) && ! empty($languages)): ?>

                                <?php $active = ' active'; ?>

                                <?php foreach ($languages as $language): ?>

                                    <div class="panel-content panel-table tab-pane<?php echo $active; ?>" id="<?php echo $language->id . '-' . $language->slug; ?>">
                                        <?php echo $view->fetch('email_templates/form_element.php', array('language' => $language)); ?>
                                    </div>

                                    <?php $active = ''; ?>
                                <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                        <div class="panel-content">
                            <div class="form-actions">
                                <?php
                                    echo $forms->submit(
                                        'submit',
                                        $lang->get('save'),
                                        array(
                                            'class' => 'btn btn-secondary'
                                        )
                                    );
                                ?>
                            </div>
                        </div>

                    </div>


                    <?php echo $forms->close(); ?>

                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php');
