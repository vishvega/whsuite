<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">

                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('report'); ?></th>
                                        <th><?php echo $lang->get('download'); ?></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td><?php echo $lang->get('all_clients'); ?></td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-report-all-clients'); ?>" class="btn btn-primary btn-small"><?php echo $lang->get('download'); ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $lang->get('all_transactions'); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="#" class="btn btn-primary btn-small" data-toggle="dropdown"><?php echo $lang->get('download'); ?> <i class="fa fa-caret-down"></i></a>
                                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                                    <li><a href="<?php echo $router->generate('admin-report-all-transactions'); ?>"><?php echo $lang->get('all'); ?></a></li>
                                                    <?php foreach($currencies as $currency): ?>
                                                        <li><a href="<?php echo $router->generate('admin-report-transactions', array('id' => $currency->id)); ?>"><?php echo $currency->code; ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php echo $lang->get('outstanding_invoices'); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="#" class="btn btn-primary btn-small" data-toggle="dropdown"><?php echo $lang->get('download'); ?> <i class="fa fa-caret-down"></i></a>
                                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                                    <li><a href="<?php echo $router->generate('admin-report-all-outstanding-invoices'); ?>"><?php echo $lang->get('all'); ?></a></li>
                                                    <?php foreach($currencies as $currency): ?>
                                                        <li><a href="<?php echo $router->generate('admin-report-outstanding-invoices', array('id' => $currency->id)); ?>"><?php echo $currency->code; ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php echo $lang->get('all_invoices'); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="#" class="btn btn-primary btn-small" data-toggle="dropdown"><?php echo $lang->get('download'); ?> <i class="fa fa-caret-down"></i></a>
                                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                                    <li><a href="<?php echo $router->generate('admin-report-all-invoices'); ?>"><?php echo $lang->get('all'); ?></a></li>
                                                    <?php foreach($currencies as $currency): ?>
                                                        <li><a href="<?php echo $router->generate('admin-report-invoices', array('id' => $currency->id)); ?>"><?php echo $currency->code; ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php echo $lang->get('12_month_income'); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="#" class="btn btn-primary btn-small" data-toggle="dropdown"><?php echo $lang->get('download'); ?> <i class="fa fa-caret-down"></i></a>
                                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                                    <?php foreach($currencies as $currency): ?>
                                                        <li><a href="<?php echo $router->generate('admin-report-12-month-income', array('id' => $currency->id)); ?>"><?php echo $currency->code; ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $view->fetch('elements/footer.php'); ?>
