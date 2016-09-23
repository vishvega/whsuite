<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary panel-tabs">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#invoice" data-toggle="tab">
                                        <?php echo $lang->get('view_invoice'); ?> (<?php echo $invoice->invoice_no; ?>)
                                    </a>
                                </li>
                                <?php if ($invoice->total_paid < $invoice->total): ?>
                                    <li><a href="#addpayment" data-toggle="tab"><?php echo $lang->get('add_payment'); ?></a></li>
                                <?php endif; ?>
                                <li><a href="#accountcredit" data-toggle="tab"><?php echo $lang->get('account_credit'); ?></a></li>
                                <li><a href="#editinvoice" data-toggle="tab"><?php echo $lang->get('edit_invoice'); ?></a></li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="panel-content tab-pane active" id="invoice">
                                <?php echo $view->fetch('invoices/invoiceTabs/invoice.php'); ?>
                            </div>

                            <?php if ($invoice->total_paid < $invoice->total): ?>
                                <div class="panel-content tab-pane" id="addpayment">
                                    <?php echo $view->fetch('invoices/invoiceTabs/addPayment.php'); ?>
                                </div>
                            <?php endif; ?>

                            <div class="panel-content tab-pane" id="accountcredit">
                                <?php echo $view->fetch('invoices/invoiceTabs/accountCredit.php'); ?>
                            </div>

                            <div class="panel-content tab-pane" id="editinvoice">
                                <?php echo $view->fetch('invoices/invoiceTabs/editInvoice.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php echo $view->fetch('elements/footer.php');
