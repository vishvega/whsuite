<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php echo $view->fetch('elements/message.php'); ?>

                            <?php echo $forms->open(array('role' => 'form', 'class' => 'form-vertical')); ?>
                                <?php echo $forms->wysiwyg('Note.note', '', array('value' => $note->note, 'form-type' => 'form-vertical')); ?>

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

    <?php echo $view->fetch('elements/footer.php');
