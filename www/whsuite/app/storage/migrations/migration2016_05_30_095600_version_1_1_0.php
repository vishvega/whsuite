<?php
namespace App\Storage\Migrations;

use \App\Libraries\BaseMigration;
use \App\Libraries\LanguageHelper;

class Migration2016_05_30_095600_version_1_1_0 extends BaseMigration
{
    public function up()
    {
        \LanguagePhrase::insert(
            array(
                array(
                    'language_id' => 1,
                    'slug' => 'no_gateways_notice',
                    'text' => 'There were no active payment gateways found.',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'language_id' => 1,
                    'slug' => 'activate_gateway_addon',
                    'text' => 'Activate A Payment Gateway Addon',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'language_id' => 1,
                    'slug' => 'currency_gateway_instructions',
                    'text' => 'To enable payment gateways for this currency, drag them from the left hand list of available currencies into the selected currencies section on the right. You can then drag them up and down to set the display order. To remove a gateway, drag it back onto the available gateways section.',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'language_id' => 1,
                    'slug' => 'available_gateways',
                    'text' => 'Available Gateways',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'language_id' => 1,
                    'slug' => 'selected_gateways',
                    'text' => 'Selected Gateways',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'language_id' => 1,
                    'slug' => 'no_currency_gateways_notice',
                    'text' => 'No payment gateways could be found. Either your payment gateways are not active, or you do not have any payment gateway addons enabled.',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );
    }

    public function down()
    {

    }
}
