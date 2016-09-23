<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <?php echo $view->fetch('clients/profileSidebar/info.php'); ?>
                </div>
                <div class="col-lg-8">
                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('note'); ?></td>
                                        <th><?php echo $lang->get('updated_at'); ?></td>
                                        <th><?php echo $lang->get('created_at'); ?></td>
                                        <th><?php echo $lang->get('edit'); ?></td>
                                        <th><?php echo $lang->get('delete'); ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if($notes->count() < 1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($notes as $note): ?>
                                    <tr>
                                        <td>
                                            <?php echo App::get('str')->limit(strip_tags(html_entity_decode($note->note)), 100, '...'); ?>
                                        </td>
                                        <td>
                                            <?php
                                            if($note->updated_at):
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $note->updated_at,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            else:
                                                echo '-';
                                            endif;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $note->created_at,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-clientnote-edit', array('id' => $client->id, 'note_id' => $note->id)); ?>" class="btn btn-primary btn-small">
                                                <?php echo $lang->get('edit_note'); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-clientnote-delete', array('id' => $client->id, 'note_id' => $note->id)); ?>" class="btn btn-danger btn-small" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>');">
                                                <?php echo $lang->get('delete_note'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right">
                                            <?php echo $pagination; ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php');
