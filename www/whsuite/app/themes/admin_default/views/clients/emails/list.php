<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">

                    <?php echo $view->fetch('clients/profileSidebar/info.php'); ?>

                </div>
                <div class="col-lg-8">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('client_emails'); ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('subject'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('date_sent'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('view'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($emails as $email):
                                    ?>
                                    <tr>
                                        <td><?php echo App::get('str')->limit($email->subject, 80); ?></td>
                                        <td>
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $email->created_at,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            ?>
                                        </td>
                                        <td class="text-right"><a class="emailModal" href="<?php echo $router->generate('admin-clientemail-view', array('id' => $client->id, 'email_id' => $email->id)); ?>"><?php echo $lang->get('view'); ?></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right"><?php echo $pagination; ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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
