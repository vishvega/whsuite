<?php

namespace App\Libraries;

class BaseMigration
{
    /**
     * date now
     */
    protected $date = null;

    /**
     * schema generator
     */
    protected $schema = null;

    /**
     * database prefix
     */
    protected $db_prefix = null;

    /**
     * setup useful stuff for the migrations
     *
     */
    public function __construct()
    {
        $this->date = \Carbon\Carbon::now();
        $this->schema = \Illuminate\Database\Capsule\Manager::schema();
        $this->db_prefix = \App::get('configs')->get('database.mysql.prefix');
    }

    /**
     * wrapper for the schema create method
     * to auto prefix tables
     *
     * @param   string      Table name to create
     * @param   function    Anonymouse function containing the schema instructions
     */
    public function createTable($table_name, $function)
    {
        $this->schema->create(
            $this->db_prefix . $table_name,
            $function
        );
    }

    /**
     * wrapper for the schema create method
     * to auto prefix tables
     *
     * @param   string      Table name to drop
     */
    public function dropTable($table_name)
    {
        $this->schema->drop(
            $this->db_prefix . $table_name
        );
    }

    /**
     * wrapper for the schema table method to perform column alterations
     *
     * @param   string          Table name
     * @param   function    Anonymouse function containing the schema instructions
     */
    public function alterTable($table_name, $function)
    {
        $this->schema->table(
            $this->db_prefix . $table_name,
            $function
        );
    }

    /**
     * wrapper for the schema rename method to amend table name
     *
     * @param   string      Old Table name
     * @param   string      New Table name
     */
    public function renameTable($old_name, $new_name)
    {
        $this->schema->rename(
            $this->db_prefix . $old_name,
            $this->db_prefix . $new_name
        );
    }

    /**
     * wrapper for being able to add raw sql
     *
     * @param   string
     * @return  string
     */
    public function raw($string)
    {
        return \Illuminate\Database\Capsule\Manager::raw($string);
    }
}
