<?php

class AddonMigration extends AppModel
{

    /**
     * primaryKey
     *
     * @var integer
     * @access protected
     */
    protected $primaryKey = 'migration';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

}
