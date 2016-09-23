<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    if (isset($message)):
                        echo $message;
                    endif;
                    ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php
                            echo $forms->open(
                            array(
                                'action' => '',
                                'method' => 'post',
                            )); ?>
                                <div id="nameserversContainer">
                                    <?php foreach($nameservers as $nameserver): ?>
                                        <?php echo $forms->input('nameservers[]', $lang->get('nameservers'), array('value' => $nameserver)); ?>
                                    <?php endforeach; ?>

                                    <?php if(count($nameservers) < 2): ?>
                                        <?php echo $forms->input('nameservers[]', $lang->get('nameservers')); ?>
                                    <?php endif; ?>
                                </div>
                                <p class="help-block"><a href="#" id="moreNameservers"><?php echo $lang->get('add_more'); ?></a></p>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')) ;?>
                                </div>


                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#moreNameservers").on('click', function(e) {
            e.preventDefault();
            var $clone = $('#nameserversContainer .form-group:last').clone();
            $clone.find('input').val('');

            $clone.appendTo('#nameserversContainer');
        });
    </script>

    <?php echo $view->fetch('elements/footer.php'); ?>
