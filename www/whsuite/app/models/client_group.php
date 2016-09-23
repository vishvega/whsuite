<?php
/**
 * Client Groups Model
 *
 * The client groups table stores the individual groups that clients can be linked to.
 *
 * @package  WHSuite-Models
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class ClientGroup extends AppSentryGroup
{
    protected $table = 'client_groups';

    public static $rules = array(
        'name' => 'required'
    );

    // slight sentry annoyance.
    // override user relation to allow it to delete staff / staff group relationships
    // as we have renamed all the users / groups for "staff"
    public function users()
    {
        return $this->belongsToMany('Client');
    }

    // relationship for us to use.
    public function Client()
    {
        return $this->hasMany('Client');
    }
}
