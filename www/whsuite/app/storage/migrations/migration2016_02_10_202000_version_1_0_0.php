<?php
namespace App\Storage\Migrations;

use \App\Libraries\BaseMigration;
use \App\Libraries\LanguageHelper;

class Migration2016_02_10_202000_version_1_0_0 extends BaseMigration
{
    public function up()
    {
        $this->alterTable(
            'menu_links',
            function($table) {
                $table->string('target', 25)
                    ->default('_self')
                    ->after('url');
            }
        );

        \LanguagePhrase::insert(
            array(
                array(
                    'language_id' => 1,
                    'slug' => 'target',
                    'text' => 'Target',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );
    }

    public function down()
    {
        $this->alterTable(
            'menu_links',
            function($table) {
                $table->dropColumn('target');
            }
        );
    }
}
