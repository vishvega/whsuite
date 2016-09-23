<?php echo $view->fetch('elements/header.php'); ?>

    <?php if (! empty($emailWarning)): ?>

        <?php
            $emailWarningList = '<ul><li>' . implode('</li><li>', $emailWarning) . '</li></ul>';
            $emailWarningIntro = '<p>' . $lang->get('email_warning_intro') . '</p>';

            echo $view->fetch(
                'elements/messages/warning.php',
                array(
                    'message_body' => $emailWarningIntro . $emailWarningList
                )
            );
        ?>

    <?php endif; ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">

                <div class="col-md-12">

                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">

                                <?php echo $view->fetch('elements/listing/tableHeader.php'); ?>

                                <tbody>

                                    <?php if (! empty($data)): ?>

                                        <?php foreach ($data as $installed): ?>

                                            <tr>
                                                <td><?php echo $installed->Language->name; ?></td>

                                                <td>
                                                    <?php
                                                        if (! empty($installed->Addon->directory)):

                                                            echo $addon_helper->getDetails($installed->Addon->directory, 'name');
                                                        else:

                                                            echo 'WHSuite';
                                                        endif;
                                                    ?>
                                                </td>

                                                <td>

                                                    <?php
                                                        if (\App\Libraries\LanguageHelper::isEnglish($installed)):

                                                            echo '<a href="#" class="btn btn-danger btn-small pull-right disabled">';
                                                                echo '<i class="fa fa-remove"></i>';
                                                                echo ' ' . $lang->get('delete');
                                                            echo '</a>';

                                                        else:
                                                            echo App::get('listingshelper')->actionButton(
                                                                $installed,
                                                                $installed->toArray(),
                                                                $columns['2'],
                                                                $actions
                                                            );
                                                        endif;
                                                    ?>

                                                </td>

                                            </tr>



                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <tr>
                                            <td colspan="3">
                                                <?php echo $lang->get('no_results_found'); ?>
                                            </td>
                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php');
