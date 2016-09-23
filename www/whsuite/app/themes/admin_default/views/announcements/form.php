<?php echo $view->fetch('elements/header.php'); ?>
    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('role' => 'form')); ?>
                                <?php echo $forms->input('data.Announcement.title', $lang->get('title')); ?>
                                <?php echo $forms->wysiwyg('data.Announcement.body', $lang->get('body')); ?>
                                <?php echo $forms->input('data.Announcement.publish_date', $lang->get('publish_date'), array('value' => $datetime)); ?>
                                <?php echo $forms->checkbox('data.Announcement.is_published', $lang->get('is_published')); ?>
                                <?php echo $forms->checkbox('data.Announcement.indvidual_language_only', $lang->get('individual_language_only')); ?>
                                <?php echo $forms->checkbox('data.Announcement.clients_only', $lang->get('clients_only')); ?>
                                <?php echo $forms->select('data.Announcement.language_id', $lang->get('language'), array('options' => $languages)); ?>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(function() {
        $( "#dataAnnouncementPublishDate" ).datetimepicker(
        {
            dateFormat: "yy-mm-dd",
            timeFormat: "H:mm:ss"
        });
    });
    </script>

    <?php echo $view->fetch('elements/footer.php');
