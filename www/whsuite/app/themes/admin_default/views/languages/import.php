<?php echo $view->fetch('elements/header.php'); ?>

    <?php if ($uploaderOn): ?>

        <div class="content-inner">
            <div class="container">
                <div class="row">

                    <div class="col-md-12">

                        <div class="panel panel-secondary">
                            <div class="panel-heading"><?php echo $title; ?></div>
                            <div class="panel-content panel-table">

                                <?php
                                    echo $forms->open(
                                        array(
                                            'action' => $router->generate(
                                                'admin-language-import'
                                            ),
                                            'class' => 'form-vertical',
                                            'method' => 'files',
                                            'role' => 'form',
                                        )
                                    );

                                    echo $forms->hidden(
                                        'data.ImportCsv.import',
                                        array(
                                            'value' => 'import-lang'
                                        )
                                    );

                                    echo $forms->file(
                                        'data.Language.0.filename',
                                        $lang->get('file_to_import')
                                    );
                                ?>

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

    <?php endif; ?>

<?php echo $view->fetch('elements/footer.php'); ?>