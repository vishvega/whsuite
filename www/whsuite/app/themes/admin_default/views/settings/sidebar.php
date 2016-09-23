<div class="panel panel-primary">
    <div class="panel-heading"><?php echo $lang->get('system_settings'); ?></div>
    <div class="panel-content">
        <ul class="nav nav-pills nav-stacked">
            <?php foreach($settings_categories as $settings_category): ?>
            <li <?php if(isset($category) && $category->id === $settings_category->id): ?>
                    class="active"
                <?php endif; ?>>
                <a href="<?php echo $router->generate('admin-settings-category', array('id' => $settings_category->id)); ?>">
                    <?php echo $lang->get($settings_category->title); ?>
                </a>
            </li>
            <?php endforeach; ?>

            <li
                <?php if($title == $lang->get('passphrase_settings')): ?>
                    class="active"
                <?php endif; ?>>

                <a href="<?php echo $router->generate('admin-settings-passphrase'); ?>"

                ><?php echo $lang->get('passphrase_settings'); ?></a>
            </li>
        </ul>
    </div>
</div>
