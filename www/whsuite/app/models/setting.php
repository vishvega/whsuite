<?php
/**
 * Settings Model
 *
 * The settings table stores system-wide settings, including addon settings.
 *
 * @package  WHSuite-Models
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class Setting extends AppModel
{
    public static $key = 'slug';
    public $incrementing = false;

    public function SettingCategory()
    {
        return $this->belongsTo('SettingCategory');
    }

}
