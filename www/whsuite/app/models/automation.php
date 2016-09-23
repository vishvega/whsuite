<?php
/**
 * Automation Model
 *
 * The automation table stores all the supported cron jobs along with
 * their last run time, and the period that they run. The period is basically
 * how many minutes gap there is between two runs.
 *
 * @package  WHSuite-Models
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class Automation extends AppModel
{
    protected $table = 'automations';

}
