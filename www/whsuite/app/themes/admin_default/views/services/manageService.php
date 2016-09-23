<?php echo $view->fetch('elements/header.php'); ?>


    <div class="content-inner">
        <div class="container">

            <div class="row">
                <div class="col-md-3">

                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('manage_service'); ?></div>
                        <div class="panel-content">
                            <ul class="nav nav-pills nav-stacked">
                                <li class="active"><a href="#overview" role="tab" data-toggle="tab"><?php echo $lang->get('overview'); ?></a></li>
                                <?php if (isset($manage_route)): ?>
                                    <li><a href="#manage" role="tab" data-toggle="tab"><?php echo $lang->get('manage'); ?></a></li>
                                <?php endif; ?>
                                <?php if (isset($enable_addons)): ?>
                                    <li><a href="#addons" role="tab" data-toggle="tab"><?php echo $lang->get('addons'); ?></a></li>
                                <?php endif; ?>
                                <?php if ($type == 'hosting' && $product->allow_ips == '1'): ?>
                                <li><a href="#ipaddresses" data-toggle="tab"><?php echo $lang->get('ip_addresses'); ?></a></li>
                                <?php endif; ?>
                                <li><a href="#editdetails" data-toggle="tab"><?php echo $lang->get('edit_details'); ?></a></li>
                            </ul>
                        </div>
                    </div>

                </div>
                <div class="col-md-9">

                    <div class="panel panel-secondary">
                        <div class="tab-content">
                            <div class="tab-pane active" id="overview">
                            <?php
                            if ($type == 'hosting'):
                                echo $view->fetch('services/manageServiceTabs/overviewHosting.php');
                            elseif ($type == 'domain'):
                                echo $view->fetch('services/manageServiceTabs/overviewDomain.php');
                            else:
                                echo $view->fetch('services/manageServiceTabs/overviewOther.php');
                            endif;
                            ?>
                            </div>
                            <?php if (isset($manage_route)): ?>
                                <div class="panel-content tab-pane" id="manage">
                                    <p class="loading"><?php echo $lang->get('loading'); ?></p>
                                </div>
                            <?php endif; ?>


                            <div class="panel-content tab-pane" id="addons">
                                <?php echo $view->fetch('services/manageServiceTabs/addons.php'); ?>
                            </div>

                            <?php if ($type == 'hosting'): ?>
                            <div class="panel-content tab-pane" id="ipaddresses">
                                <?php echo $view->fetch('services/manageServiceTabs/ipAddresses.php'); ?>
                            </div>
                            <?php endif; ?>

                            <div class="panel-content tab-pane" id="editdetails">
                                <?php echo $view->fetch('services/manageServiceTabs/editDetails.php'); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php if (isset($manage_route)): ?>
    <script>
    $( "#manage" ).load( "<?php echo $manage_route; ?>", function() {

    });
    </script>
<?php endif; ?>

<?php echo $view->fetch('elements/footer.php');
