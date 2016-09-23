<?php
namespace App\Storage\Migrations;

use \App\Libraries\BaseMigration;
use \App\Libraries\LanguageHelper;

class Migration2016_04_07_194000_version_1_0_1 extends BaseMigration
{
    public function up()
    {
        $this->alterTable(
            'automations',
            function($table) {
                $table->datetime('start_time')
                    ->default('0000-00-00 00:00:00')
                    ->after('last_run');
            }
        );
    }

    public function down()
    {
        $this->alterTable(
            'automations',
            function($table) {
                $table->dropColumn('start_time');
            }
        );
    }
}
