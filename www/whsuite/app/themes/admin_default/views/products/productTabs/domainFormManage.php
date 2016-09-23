<?php echo $forms->input('Domain.extension', $lang->get('domain_extension'), array('disabled' => 'disabled')); ?>
<?php
if($addon):
    App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->productFields($extension->id);
endif;
?>
