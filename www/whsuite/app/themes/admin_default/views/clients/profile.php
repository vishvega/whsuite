<?php echo $view->fetch('elements/header.php'); ?>
<?php
    $store_credit_cards = \App::get('configs')->get('settings.billing.store_credit_cards');
    $store_ach = \App::get('configs')->get('settings.billing.store_ach');
?>

    <div class="content-inner">
        <div class="container">

            <div class="row">
                <div class="col-md-3">
                    <?php
                        echo $view->fetch(
                            'clients/profileSidebar/profileSidebar.php',
                            array(
                                'store_credit_cards' => $store_credit_cards,
                                'store_ach' => $store_ach
                            )
                        );
                    ?>
                </div>

                <div class="col-md-9 tab-content">
                    <div class="tab-pane fade in active" id="overview">
                        <?php echo $view->fetch('clients/clientTabs/overview.php'); ?>
                    </div>
                    <div class="tab-pane fade" id="services">
                        <?php echo $view->fetch('clients/clientTabs/services.php'); ?>
                    </div>

                    <div class="tab-pane fade" id="invoices">
                        <?php echo $view->fetch('clients/clientTabs/invoices.php'); ?>
                    </div>

                    <div class="tab-pane fade" id="transactions">
                        <?php echo $view->fetch('clients/clientTabs/transactions.php'); ?>
                    </div>

                    <?php if ($store_credit_cards == 1 || $store_ach == 1): ?>
                        <div class="tab-pane fade" id="ccachaccounts">
                            <?php echo $view->fetch('clients/clientTabs/ccAchAccounts.php'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="tab-pane fade" id="emails">
                        <?php echo $view->fetch('clients/clientTabs/emails.php'); ?>
                    </div>

                    <div class="tab-pane fade" id="profile">
                        <?php echo $view->fetch('clients/clientTabs/editProfile.php'); ?>
                    </div>

                    <div class="tab-pane fade" id="notes">
                        <?php echo $view->fetch('clients/clientTabs/notes.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="emailModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo $lang->get('view_email'); ?></h4>
                </div>
                <div class="modal-body">
                    <p><?php echo $lang->get('loading'); ?></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal"><?php echo $lang->get('close'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $('.emailModal').click(function(e){
        e.preventDefault();

        $('.modal-body').load(this.href,function(result){
            $('#emailModal').modal({show:true});
        });
    });
    </script>
    <?php echo $view->fetch('elements/footer.php');
