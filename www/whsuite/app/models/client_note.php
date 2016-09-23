<?php
/**
 * Client Notes Model
 *
 * The client notes table stores all note entries about a client.
 *
 * @package  WHSuite-Models
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class ClientNote extends AppModel
{

    public static $rules = array(
        'note' => 'required'
    );

    public function Client()
    {
        return $this->belongsTo('Client');
    }

}
