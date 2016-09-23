<?php echo $view->fetch('elements/header.php'); ?>

<div class="content-full">
    <div class="row">
        <div class="col-lg-3 col-md-3">
            <?php echo $view->display('settings/sidebar.php'); ?>
        </div>

        <div class="col-lg-9 col-md-9">

            <div class="panel panel-secondary">
                <div class="panel-heading"><?php echo $title; ?></div>
                <div class="panel-content">
                    <?php echo $forms->open(array('action' => $router->generate('admin-settings-category', array('id' => $category->id)))); ?>

                    <?php foreach($settings as $setting): ?>

                        <?php
                            if($setting->field_type == 'textarea'):
                                echo $forms->textarea('Setting.'.$setting->slug.'.value', $lang->get($setting->title), array('placeholder' => $lang->get($setting->placeholder)));
                            elseif($setting->field_type == 'wysiwyg'):
                                echo $forms->wysiwyg('Setting.'.$setting->slug.'.value', $lang->get($setting->title), array('placeholder' => $lang->get($setting->placeholder)));
                            elseif($setting->field_type == 'checkbox'):
                                echo $forms->checkbox('Setting.'.$setting->slug.'.value', $lang->get($setting->title), array('placeholder' => $lang->get($setting->placeholder)));
                            elseif($setting->field_type == 'select'):
                                echo $forms->select('Setting.'.$setting->slug.'.value', $lang->get($setting->title), array('placeholder' => $lang->get($setting->placeholder), 'options' => json_decode($setting->options, true)));
                            elseif($setting->field_type == 'multiselect'):
                                echo $forms->multiselect('Setting.'.$setting->slug.'.value', $lang->get($setting->title), array('placeholder' => $lang->get($setting->placeholder), 'options' => json_decode($setting->options, true)));
                            else:
                                echo $forms->input('Setting.'.$setting->slug.'.value', $lang->get($setting->title), array('placeholder' => $lang->get($setting->placeholder)));
                            endif;
                        ?>

                        <?php if($setting->description !=''): ?>
                            <span class="help-block"><?php echo $lang->get($setting->description); ?></span>
                        <?php endif; ?>

                    <?php endforeach; ?>

                    <div class="form-actions">
                        <?php echo $forms->submit('submit', $lang->get('save')); ?>
                    </div>
                    <?php echo $forms->close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
