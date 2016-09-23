<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $lang->get('announcement'); ?></div>
            <div class="panel-body">
                <h1><?php echo $announcement->title; ?></h1>
                <div class="well well-sm">
                    <b><?php echo $lang->get('posted'); ?></b>
                    <?php
                        $publishDate = \Carbon\Carbon::parse(
                            $announcement->publish_date,
                            $date['timezone']
                        );
                        echo $publishDate->format($date['full_datetime']);
                    ?>
                </div>

                <?php echo html_entity_decode($announcement->body); ?>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
