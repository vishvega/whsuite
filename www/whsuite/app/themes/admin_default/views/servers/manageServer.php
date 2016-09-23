<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary panel-tabs">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#overview" data-toggle="tab">
                                        <?php echo $lang->get('overview'); ?>
                                    </a>
                                </li>
                                <?php if(isset($manage_route)): ?><li><a href="#manage" data-toggle="tab"><?php echo $lang->get('manage_server'); ?></a></li><?php endif; ?>
                                <li><a href="#ipaddresses" data-toggle="tab"><?php echo $lang->get('ip_addresses'); ?></a></li>
                                <li><a href="#accounts" data-toggle="tab"><?php echo $lang->get('accounts'); ?> (<?php echo $accounts->count(); ?>)</a></li>
                                <li><a href="#nameservers" data-toggle="tab"><?php echo $lang->get('nameservers'); ?></a></li>
                                <li><a href="#editdetails" data-toggle="tab"><?php echo $lang->get('edit_details'); ?></a></li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="panel-content tab-pane active" id="overview">
                                <?php echo $view->fetch('servers/serverTabs/overview.php'); ?>
                            </div>

                            <?php if(isset($manage_route)): ?>
                                <div class="panel-content tab-pane" id="manage">
                                    <p class="loading"><i class="fa fa-cog fa-spin"></i> <?php echo $lang->get('loading'); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="panel-content tab-pane" id="ipaddresses">
                                <?php echo $view->fetch('servers/serverTabs/ipAddresses.php'); ?>
                            </div>

                            <div class="panel-content tab-pane" id="accounts">
                                <?php echo $view->fetch('servers/serverTabs/accounts.php'); ?>
                            </div>

                            <div class="panel-content tab-pane" id="nameservers">
                                <?php echo $view->fetch('servers/serverTabs/nameservers.php'); ?>
                            </div>

                            <div class="panel-content tab-pane" id="editdetails">
                                <?php echo $view->fetch('servers/serverTabs/editDetails.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if(isset($manage_route)): ?>
    <script>

    var errorMsg = function() {
         $("#manage").html('<div class="alert alert-danger"><?php echo $lang->get('server_connection_failed'); ?></div>');
       }

    var loadTimeout = setTimeout(errorMsg, 15100);

    $(function() {
        $.ajax({
            url: "<?php echo $manage_route; ?>",
            timeout: 60000,
            success: function(data) {
                $("#manage .loading").fadeOut("fast", function() {
                    $('#manage').html(data);
                });
                clearTimeout(loadTimeout);
            }
        });
    });
    </script>
<?php endif; ?>

<?php echo $view->fetch('elements/footer.php'); ?>
