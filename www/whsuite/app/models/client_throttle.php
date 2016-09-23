<?php
/**
 * Client Throttle Model
 *
 * The client throttles table is used to store login attempts so we can protect
 * the user account from bruit force access attempts.
 *
 * @package  WHSuite-Models
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class ClientThrottle extends AppSentryThrottle
{
    protected $table = 'client_throttles';

    public function user()
    {
        return $this->belongsTo('Client', 'user_id');
    }
}
