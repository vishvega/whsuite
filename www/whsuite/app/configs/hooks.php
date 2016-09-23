<?php

/**
 * listen for a paid invoice and add the account credit
 */
\App::get('hooks')->startListening(
    'invoice-paid',
    'add_account_credit',
    '\App\Libraries\Transactions::addAccountCredit'
);