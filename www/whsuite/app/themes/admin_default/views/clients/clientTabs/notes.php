<div class="panel panel-secondary">
    <div class="panel-heading"><?php echo $lang->get('notes'); ?></div>
    <div class="panel-content">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo $lang->get('note'); ?></th>
                    <th><?php echo $lang->get('date_created'); ?></th>
                    <th class="text-right"><?php echo $lang->get('edit'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if($notes->count() > 0): ?>
                <?php foreach ($notes as $note): ?>
                <tr>
                    <td><?php echo App::get('str')->limit(strip_tags(html_entity_decode($note->note)), 40); ?></td>
                    <td>
                        <?php
                            $Carbon = \Carbon\Carbon::parse(
                                $note->created_at,
                                $date['timezone']
                            );
                            echo $Carbon->format($date['short_date']);
                        ?>
                    </td>
                    <td class="text-right">
                        <a href="<?php echo $router->generate('admin-clientnote-edit', array('id' => $client->id, 'note_id' => $note->id)); ?>" class="btn btn-primary btn-small">
                            <?php echo $lang->get('edit'); ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <div class="row">
                            <div class="col-sm-6 text-center">
                                <a href="<?php echo $router->generate('admin-clientnote-add', array('id' => $client->id)); ?>" class="btn btn-primary btn-small">
                                    <i class="fa fa-plus"></i> <?php echo $lang->get('add_note'); ?>
                                </a>
                            </div>
                            <div class="col-sm-6 text-center">
                                <a href="<?php echo $router->generate('admin-clientnote', array('id' => $client->id)); ?>" class="btn btn-primary btn-small">
                                    <i class="fa fa-list"></i> <?php echo $lang->get('all_notes'); ?>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
