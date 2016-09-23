<?php
namespace App\Storage\Migrations;

use \App\Libraries\BaseMigration;
use \App\Libraries\LanguageHelper;

class Migration2015_11_18_230000_version_1_0_0_data extends BaseMigration
{
    public function up()
    {
        // Populate the Automation table with the default run times
        \Automation::insert(
            array(
                array(
                    'slug' => '5_minutes',
                    'run_period' => '5',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '15_minutes',
                    'run_period' => '15',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '30_minutes',
                    'run_period' => '30',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '1_hour',
                    'run_period' => '60',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '6_hours',
                    'run_period' => '360',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '12_hours',
                    'run_period' => '720',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '24_hours',
                    'run_period' => '1440',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '48_hours',
                    'run_period' => '2880',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '7_days',
                    'run_period' => '10080',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '30_days',
                    'run_period' => '43200',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '60_days',
                    'run_period' => '86400',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '90_days',
                    'run_period' => '129600',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '180_days',
                    'run_period' => '259200',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => '365_days',
                    'run_period' => '525600',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // insert the billing periods
        \BillingPeriod::insert(
            array(
                array(
                    'name' => 'Monthly',
                    'days' => 30,
                    'sort' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'name' => 'Quarterly',
                    'days' => 91,
                    'sort' => 2,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'name' => 'Semi-Annual',
                    'days' => 182,
                    'sort' => 3,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'name' => 'Annual',
                    'days' => 365,
                    'sort' => 4,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'name' => 'Biennial',
                    'days' => 730,
                    'sort' => 5,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'name' => 'Triennial',
                    'days' => 1095,
                    'sort' => 6,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'name' => 'Quadrennial',
                    'days' => 1460,
                    'sort' => 7,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'name' => 'One-Time',
                    'days' => 0,
                    'sort' => 0,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // todo: insert countries
        \Country::insert(
            array(
                array(
                    'iso_code' => 'AD',
                    'name' => 'Andorra',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AE',
                    'name' => 'United Arab Emirates',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AF',
                    'name' => 'Afghanistan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AG',
                    'name' => 'Antigua and Barbuda',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AI',
                    'name' => 'Anguilla',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AL',
                    'name' => 'Albania',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AM',
                    'name' => 'Armenia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AO',
                    'name' => 'Angola',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AP',
                    'name' => 'Asia/Pacific Region',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AQ',
                    'name' => 'Antarctica',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AR',
                    'name' => 'Argentina',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AS',
                    'name' => 'American Samoa',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AT',
                    'name' => 'Austria',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AU',
                    'name' => 'Australia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AW',
                    'name' => 'Aruba',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AX',
                    'name' => 'Aland Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'AZ',
                    'name' => 'Azerbaijan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BA',
                    'name' => 'Bosnia and Herzegovina',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BB',
                    'name' => 'Barbados',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BD',
                    'name' => 'Bangladesh',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BE',
                    'name' => 'Belgium',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BF',
                    'name' => 'Burkina Faso',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BG',
                    'name' => 'Bulgaria',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BH',
                    'name' => 'Bahrain',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BI',
                    'name' => 'Burundi',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BJ',
                    'name' => 'Benin',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BL',
                    'name' => 'Saint Barthelemy',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BM',
                    'name' => 'Bermuda',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BN',
                    'name' => 'Brunei Darussalam',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BO',
                    'name' => 'Bolivia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BQ',
                    'name' => 'Bonaire, Saint Eustatius and Saba',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BR',
                    'name' => 'Brazil',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BS',
                    'name' => 'Bahamas',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BT',
                    'name' => 'Bhutan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BW',
                    'name' => 'Botswana',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BY',
                    'name' => 'Belarus',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'BZ',
                    'name' => 'Belize',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CA',
                    'name' => 'Canada',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CC',
                    'name' => 'Cocos (Keeling) Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CD',
                    'name' => 'Congo, The Democratic Republic of the',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CF',
                    'name' => 'Central African Republic',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CG',
                    'name' => 'Congo',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CH',
                    'name' => 'Switzerland',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CI',
                    'name' => 'Cote D\'Ivoire',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CK',
                    'name' => 'Cook Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CL',
                    'name' => 'Chile',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CM',
                    'name' => 'Cameroon',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CN',
                    'name' => 'China',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CO',
                    'name' => 'Colombia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CR',
                    'name' => 'Costa Rica',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CU',
                    'name' => 'Cuba',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CV',
                    'name' => 'Cape Verde',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CW',
                    'name' => 'Curacao',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CX',
                    'name' => 'Christmas Island',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CY',
                    'name' => 'Cyprus',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'CZ',
                    'name' => 'Czech Republic',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'DE',
                    'name' => 'Germany',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'DJ',
                    'name' => 'Djibouti',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'DK',
                    'name' => 'Denmark',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'DM',
                    'name' => 'Dominica',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'DO',
                    'name' => 'Dominican Republic',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'DZ',
                    'name' => 'Algeria',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'EC',
                    'name' => 'Ecuador',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'EE',
                    'name' => 'Estonia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'EG',
                    'name' => 'Egypt',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'EH',
                    'name' => 'Western Sahara',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'ER',
                    'name' => 'Eritrea',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'ES',
                    'name' => 'Spain',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'ET',
                    'name' => 'Ethiopia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'EU',
                    'name' => 'Europe',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'FI',
                    'name' => 'Finland',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'FJ',
                    'name' => 'Fiji',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'FK',
                    'name' => 'Falkland Islands (Malvinas)',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'FM',
                    'name' => 'Micronesia, Federated States of',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'FO',
                    'name' => 'Faroe Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'FR',
                    'name' => 'France',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GA',
                    'name' => 'Gabon',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GB',
                    'name' => 'United Kingdom',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GD',
                    'name' => 'Grenada',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GE',
                    'name' => 'Georgia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GF',
                    'name' => 'French Guiana',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GG',
                    'name' => 'Guernsey',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GH',
                    'name' => 'Ghana',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GI',
                    'name' => 'Gibraltar',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GL',
                    'name' => 'Greenland',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GM',
                    'name' => 'Gambia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GN',
                    'name' => 'Guinea',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GP',
                    'name' => 'Guadeloupe',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GQ',
                    'name' => 'Equatorial Guinea',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GR',
                    'name' => 'Greece',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GS',
                    'name' => 'South Georgia and the South Sandwich Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GT',
                    'name' => 'Guatemala',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GU',
                    'name' => 'Guam',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GW',
                    'name' => 'Guinea-Bissau',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'GY',
                    'name' => 'Guyana',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'HK',
                    'name' => 'Hong Kong',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'HN',
                    'name' => 'Honduras',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'HR',
                    'name' => 'Croatia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'HT',
                    'name' => 'Haiti',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'HU',
                    'name' => 'Hungary',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'ID',
                    'name' => 'Indonesia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'IE',
                    'name' => 'Ireland',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'IL',
                    'name' => 'Israel',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'IM',
                    'name' => 'Isle of Man',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'IN',
                    'name' => 'India',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'IO',
                    'name' => 'British Indian Ocean Territory',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'IQ',
                    'name' => 'Iraq',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'IR',
                    'name' => 'Iran, Islamic Republic of',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'IS',
                    'name' => 'Iceland',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'IT',
                    'name' => 'Italy',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'JE',
                    'name' => 'Jersey',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'JM',
                    'name' => 'Jamaica',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'JO',
                    'name' => 'Jordan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'JP',
                    'name' => 'Japan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KE',
                    'name' => 'Kenya',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KG',
                    'name' => 'Kyrgyzstan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KH',
                    'name' => 'Cambodia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KI',
                    'name' => 'Kiribati',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KM',
                    'name' => 'Comoros',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KN',
                    'name' => 'Saint Kitts and Nevis',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KP',
                    'name' => 'Korea, Democratic People\'s Republic of',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KR',
                    'name' => 'Korea, Republic of',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KW',
                    'name' => 'Kuwait',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KY',
                    'name' => 'Cayman Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'KZ',
                    'name' => 'Kazakhstan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LA',
                    'name' => 'Lao People\'s Democratic Republic',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LB',
                    'name' => 'Lebanon',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LC',
                    'name' => 'Saint Lucia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LI',
                    'name' => 'Liechtenstein',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LK',
                    'name' => 'Sri Lanka',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LR',
                    'name' => 'Liberia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LS',
                    'name' => 'Lesotho',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LT',
                    'name' => 'Lithuania',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LU',
                    'name' => 'Luxembourg',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LV',
                    'name' => 'Latvia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'LY',
                    'name' => 'Libya',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MA',
                    'name' => 'Morocco',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MC',
                    'name' => 'Monaco',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MD',
                    'name' => 'Moldova, Republic of',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'ME',
                    'name' => 'Montenegro',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MF',
                    'name' => 'Saint Martin',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MG',
                    'name' => 'Madagascar',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MH',
                    'name' => 'Marshall Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MK',
                    'name' => 'Macedonia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'ML',
                    'name' => 'Mali',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MM',
                    'name' => 'Myanmar',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MN',
                    'name' => 'Mongolia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MO',
                    'name' => 'Macau',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MP',
                    'name' => 'Northern Mariana Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MQ',
                    'name' => 'Martinique',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MR',
                    'name' => 'Mauritania',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MS',
                    'name' => 'Montserrat',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MT',
                    'name' => 'Malta',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MU',
                    'name' => 'Mauritius',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MV',
                    'name' => 'Maldives',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MW',
                    'name' => 'Malawi',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MX',
                    'name' => 'Mexico',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MY',
                    'name' => 'Malaysia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'MZ',
                    'name' => 'Mozambique',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NA',
                    'name' => 'Namibia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NC',
                    'name' => 'New Caledonia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NE',
                    'name' => 'Niger',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NF',
                    'name' => 'Norfolk Island',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NG',
                    'name' => 'Nigeria',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NI',
                    'name' => 'Nicaragua',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NL',
                    'name' => 'Netherlands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NO',
                    'name' => 'Norway',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NP',
                    'name' => 'Nepal',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NR',
                    'name' => 'Nauru',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NU',
                    'name' => 'Niue',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'NZ',
                    'name' => 'New Zealand',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'OM',
                    'name' => 'Oman',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PA',
                    'name' => 'Panama',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PE',
                    'name' => 'Peru',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PF',
                    'name' => 'French Polynesia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PG',
                    'name' => 'Papua New Guinea',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PH',
                    'name' => 'Philippines',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PK',
                    'name' => 'Pakistan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PL',
                    'name' => 'Poland',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PM',
                    'name' => 'Saint Pierre and Miquelon',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PN',
                    'name' => 'Pitcairn Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PR',
                    'name' => 'Puerto Rico',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PS',
                    'name' => 'Palestinian Territory',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PT',
                    'name' => 'Portugal',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PW',
                    'name' => 'Palau',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'PY',
                    'name' => 'Paraguay',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'QA',
                    'name' => 'Qatar',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'RE',
                    'name' => 'Reunion',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'RO',
                    'name' => 'Romania',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'RS',
                    'name' => 'Serbia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'RU',
                    'name' => 'Russian Federation',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'RW',
                    'name' => 'Rwanda',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SA',
                    'name' => 'Saudi Arabia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SB',
                    'name' => 'Solomon Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SC',
                    'name' => 'Seychelles',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SD',
                    'name' => 'Sudan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SE',
                    'name' => 'Sweden',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SG',
                    'name' => 'Singapore',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SH',
                    'name' => 'Saint Helena',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SI',
                    'name' => 'Slovenia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SJ',
                    'name' => 'Svalbard and Jan Mayen',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SK',
                    'name' => 'Slovakia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SL',
                    'name' => 'Sierra Leone',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SM',
                    'name' => 'San Marino',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SN',
                    'name' => 'Senegal',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SO',
                    'name' => 'Somalia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SR',
                    'name' => 'Suriname',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SS',
                    'name' => 'South Sudan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'ST',
                    'name' => 'Sao Tome and Principe',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SV',
                    'name' => 'El Salvador',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SX',
                    'name' => 'Sint Maarten (Dutch part)',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SY',
                    'name' => 'Syrian Arab Republic',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'SZ',
                    'name' => 'Swaziland',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TC',
                    'name' => 'Turks and Caicos Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TD',
                    'name' => 'Chad',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TF',
                    'name' => 'French Southern Territories',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TG',
                    'name' => 'Togo',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TH',
                    'name' => 'Thailand',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TJ',
                    'name' => 'Tajikistan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TK',
                    'name' => 'Tokelau',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TL',
                    'name' => 'Timor-Leste',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TM',
                    'name' => 'Turkmenistan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TN',
                    'name' => 'Tunisia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TO',
                    'name' => 'Tonga',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TR',
                    'name' => 'Turkey',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TT',
                    'name' => 'Trinidad and Tobago',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TV',
                    'name' => 'Tuvalu',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TW',
                    'name' => 'Taiwan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'TZ',
                    'name' => 'Tanzania, United Republic of',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'UA',
                    'name' => 'Ukraine',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'UG',
                    'name' => 'Uganda',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'UM',
                    'name' => 'United States Minor Outlying Islands',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'US',
                    'name' => 'United States',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'UY',
                    'name' => 'Uruguay',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'UZ',
                    'name' => 'Uzbekistan',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'VA',
                    'name' => 'Holy See (Vatican City State)',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'VC',
                    'name' => 'Saint Vincent and the Grenadines',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'VE',
                    'name' => 'Venezuela',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'VG',
                    'name' => 'Virgin Islands, British',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'VI',
                    'name' => 'Virgin Islands, U.S.',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'VN',
                    'name' => 'Vietnam',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'VU',
                    'name' => 'Vanuatu',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'WF',
                    'name' => 'Wallis and Futuna',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'WS',
                    'name' => 'Samoa',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'YE',
                    'name' => 'Yemen',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'YT',
                    'name' => 'Mayotte',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'ZA',
                    'name' => 'South Africa',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'ZM',
                    'name' => 'Zambia',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'iso_code' => 'ZW',
                    'name' => 'Zimbabwe',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // insert the currencies
        \Currency::insert(
            array(
                array(
                    'code' => 'USD',
                    'prefix' => '$',
                    'suffix' => '',
                    'decimals' => 2,
                    'decimal_point' => '.',
                    'thousand_separator' => ',',
                    'conversion_rate' => '1.00',
                    'auto_update' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'code' => 'GBP',
                    'prefix' => '&pound;',
                    'suffix' => '',
                    'decimals' => 2,
                    'decimal_point' => '.',
                    'thousand_separator' => ',',
                    'conversion_rate' => '1.00',
                    'auto_update' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'code' => 'EUR',
                    'prefix' => '€',
                    'suffix' => '',
                    'decimals' => 2,
                    'decimal_point' => '.',
                    'thousand_separator' => ',',
                    'conversion_rate' => '1.00',
                    'auto_update' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // insert default data groups
        \DataGroup::insert(
            array(
                array(
                    'slug' => 'client_fields',
                    'name' => 'client_custom_fields',
                    'addon_id' => 0,
                    'is_editable' => 1,
                    'is_active' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'product_group_fields',
                    'name' => 'product_group_fields',
                    'addon_id' => 0,
                    'is_editable' => 1,
                    'is_active' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // insert menu groups
        \MenuGroup::insert(
            array(
                array(
                    'name' => 'Admin Menu',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'name' => 'Client Menu',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // insert menu links
        \MenuLink::insert(
            array(
                array(
                    'menu_group_id' => 1,
                    'title' => 'dashboard',
                    'parent_id' => 0,
                    'is_link' => 0,
                    'url' => 'admin-home',
                    'sort' => 1,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'billing',
                    'parent_id' => 0,
                    'is_link' => 1,
                    'url' => '#',
                    'sort' => 2,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'services',
                    'parent_id' => 2,
                    'is_link' => 0,
                    'url' => 'admin-services',
                    'sort' => 1,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'orders',
                    'parent_id' => 2,
                    'is_link' => 0,
                    'url' => 'admin-order',
                    'sort' => 2,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'invoices',
                    'parent_id' => 2,
                    'is_link' => 0,
                    'url' => 'admin-invoice',
                    'sort' => 3,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'transactions',
                    'parent_id' => 2,
                    'is_link' => 0,
                    'url' => 'admin-transactions',
                    'sort' => 4,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'reports',
                    'parent_id' => 2,
                    'is_link' => 0,
                    'url' => 'admin-reports',
                    'sort' => 5,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'clients',
                    'parent_id' => 0,
                    'is_link' => 1,
                    'url' => '#',
                    'sort' => 3,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'client_management',
                    'parent_id' => 8,
                    'is_link' => 0,
                    'url' => 'admin-client',
                    'sort' => 1,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'add_client',
                    'parent_id' => 8,
                    'is_link' => 0,
                    'url' => 'admin-client-add',
                    'sort' => 2,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'announcements',
                    'parent_id' => 8,
                    'is_link' => 0,
                    'url' => 'admin-announcement',
                    'sort' => 3,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'products',
                    'parent_id' => 0,
                    'is_link' => 1,
                    'url' => '#',
                    'sort' => 4,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'server_management',
                    'parent_id' => 12,
                    'is_link' => 0,
                    'url' => 'admin-server',
                    'sort' => 1,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'product_management',
                    'parent_id' => 12,
                    'is_link' => 0,
                    'url' => 'admin-product',
                    'sort' => 2,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'domainextension_management',
                    'parent_id' => 12,
                    'is_link' => 0,
                    'url' => 'admin-domainextension',
                    'sort' => 3,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'settings',
                    'parent_id' => 0,
                    'is_link' => 1,
                    'url' => '#',
                    'sort' => 5,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'system_settings',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-settings',
                    'sort' => 1,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'taxlevel_management',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-taxlevel',
                    'sort' => 2,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'staff_management',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-staff',
                    'sort' => 3,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'action_logs',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-action-logs',
                    'sort' => 4,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'custom_field_management',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-custom-fields',
                    'sort' => 5,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'currency_management',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-currency',
                    'sort' => 6,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'emailtemplate_management',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-emailtemplate',
                    'sort' => 7,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'billingperiod_management',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-billingperiod',
                    'sort' => 8,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'addon_management',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-addon',
                    'sort' => 9,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'menu_management',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-menus',
                    'sort' => 10,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'gateway_management',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-gateway',
                    'sort' => 11,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 1,
                    'title' => 'language_management',
                    'parent_id' => 16,
                    'is_link' => 0,
                    'url' => 'admin-language',
                    'sort' => 2,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'home',
                    'parent_id' => 0,
                    'is_link' => 0,
                    'url' => 'client-home',
                    'sort' => 1,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'new_order',
                    'parent_id' => 0,
                    'is_link' => 0,
                    'url' => 'client-order',
                    'sort' => 2,
                    'clients_only' => 0,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'my_account',
                    'parent_id' => 0,
                    'is_link' => 1,
                    'url' => '#',
                    'sort' => 3,
                    'clients_only' => 1,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'overview',
                    'parent_id' => 31,
                    'is_link' => 0,
                    'url' => 'client-home',
                    'sort' => 1,
                    'clients_only' => 1,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'my_services',
                    'parent_id' => 31,
                    'is_link' => 0,
                    'url' => 'client-services',
                    'sort' => 2,
                    'clients_only' => 1,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'my_invoices',
                    'parent_id' => 31,
                    'is_link' => 0,
                    'url' => 'client-invoices',
                    'sort' => 3,
                    'clients_only' => 1,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'manage_billing_details',
                    'parent_id' => 31,
                    'is_link' => 0,
                    'url' => 'client-billing',
                    'sort' => 4,
                    'clients_only' => 1,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'payment_history',
                    'parent_id' => 31,
                    'is_link' => 0,
                    'url' => 'client-payment-history',
                    'sort' => 5,
                    'clients_only' => 1,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'domain_contacts',
                    'parent_id' => 31,
                    'is_link' => 0,
                    'url' => 'client-contacts',
                    'sort' => 6,
                    'clients_only' => 1,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'profile',
                    'parent_id' => 31,
                    'is_link' => 0,
                    'url' => 'client-profile',
                    'sort' => 7,
                    'clients_only' => 1,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'menu_group_id' => 2,
                    'title' => 'logout',
                    'parent_id' => 31,
                    'is_link' => 0,
                    'url' => 'client-logout',
                    'sort' => 8,
                    'clients_only' => 1,
                    'class' => '',
                    'addon_id' => null,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // insert product types
        \ProductType::insert(
            array(
                array(
                    'name' => 'Shared Hosting',
                    'slug' => 'shared-hosting',
                    'description' => 'Shared hosting account',
                    'is_hosting' => 1,
                    'is_domain' => 0,
                    'addon_id' => 0,
                    'sort' => 0,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'name' => 'Domain',
                    'slug' => 'domain',
                    'description' => 'Domain Registration',
                    'is_hosting' => 0,
                    'is_domain' => 1,
                    'addon_id' => 0,
                    'sort' => 0,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // insert settings categories
        \SettingCategory::insert(
            array(
                array(
                    'slug' => 'general',
                    'title' => 'general_settings',
                    'is_visible' => 1,
                    'sort' => 1,
                    'addon_id' => 0,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'billing',
                    'title' => 'billing_settings',
                    'is_visible' => 1,
                    'sort' => 2,
                    'addon_id' => 0,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'mail',
                    'title' => 'mail_settings',
                    'is_visible' => 1,
                    'sort' => 3,
                    'addon_id' => 0,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'localization',
                    'title' => 'localization_settings',
                    'is_visible' => 1,
                    'sort' => 4,
                    'addon_id' => 0,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'development',
                    'title' => 'development_settings',
                    'is_visible' => 0,
                    'sort' => 99,
                    'addon_id' => 0,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'frontend',
                    'title' => 'frontend_settings',
                    'is_visible' => 1,
                    'sort' => 5,
                    'addon_id' => 0,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // todo: insert settings
        \Setting::insert(
            array(
                array(
                    'slug' => 'admin_theme',
                    'title' => 'Admin Theme',
                    'description' => 'The theme folder to be used in the admin area',
                    'field_type' => 'text',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => 'e.g admin_default',
                    'setting_category_id' => '5',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => 'admin_default',
                    'default_value' => 'admin_default',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'client_theme',
                    'title' => 'Client Theme Folder',
                    'description' => 'Client area and frontend theme',
                    'field_type' => 'text',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '6',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '3',
                    'value' => 'client',
                    'default_value' => 'client',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'default_currency',
                    'title' => 'Default Currency',
                    'description' => 'Default currency code to use',
                    'field_type' => 'text',
                    'rules' => 'max:3|min:3',
                    'options' => '',
                    'placeholder' => 'e.g USD',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '1',
                    'value' => 'USD',
                    'default_value' => 'USD',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'domain_invoice_days',
                    'title' => 'Domain Invoice Days Before Renewal',
                    'description' => 'How many days in advance an invoice for a domain is generated',
                    'field_type' => 'text',
                    'rules' => 'integer',
                    'options' => '',
                    'placeholder' => 'e.g 30',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '4',
                    'value' => '60',
                    'default_value' => '30',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'email_signature_html',
                    'title' => 'HTML Email Signature',
                    'description' => '',
                    'field_type' => 'wysiwyg',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => '<p>Regards,</p> <p>YourSite.com</p>',
                    'default_value' => '<p>Regards,</p> <p>YourSite.com</p>',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'email_signature_plaintext',
                    'title' => 'Plaintext Email Signature',
                    'description' => '',
                    'field_type' => 'textarea',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => 'Regards,\r\n\r\n YourSite.com',
                    'default_value' => 'Regards,\r\n\r\n YourSite.com',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'full_datetime_format',
                    'title' => 'Full Date/Time Format',
                    'description' => 'The long format to use for date and time display.',
                    'field_type' => 'text',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => 'e.g F jS, Y g:i a (T)',
                    'setting_category_id' => '4',
                    'editable' => '0',
                    'required' => '0',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => 'F jS, Y g:i a (T)',
                    'default_value' => 'F jS, Y g:i a (T)',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'full_date_format',
                    'title' => 'Full Date Format',
                    'description' => 'The full date format',
                    'field_type' => 'text',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => 'e.g jS, Y',
                    'setting_category_id' => '4',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => 'jS, Y',
                    'default_value' => 'jS, Y',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'invoice_from',
                    'title' => 'PDF Invoice From Address',
                    'description' => 'This will be added as your company address to invoices.',
                    'field_type' => 'textarea',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => 'YourCompanyName 123 Web Host Road Hostville 54321',
                    'default_value' => 'YourCompanyName 123 Web Host Road Hostville 54321',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'mail_sendmail_path',
                    'title' => 'Sendmail Path',
                    'description' => '',
                    'field_type' => 'text',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '0',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => '/usr/sbin/sendmail',
                    'default_value' => '/usr/sbin/sendmail',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'mail_smtp_host',
                    'title' => 'SMTP Host',
                    'description' => '',
                    'field_type' => 'text',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => 'e.g smtp.example.com',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '0',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => '',
                    'default_value' => '',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'mail_smtp_password',
                    'title' => 'SMTP Password',
                    'description' => '',
                    'field_type' => 'text',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '0',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => '',
                    'default_value' => '',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'mail_smtp_port',
                    'title' => 'SMTP Port',
                    'description' => '',
                    'field_type' => 'text',
                    'rules' => 'integer',
                    'options' => '',
                    'placeholder' => 'e.g 25',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '0',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => '25',
                    'default_value' => '25',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'mail_smtp_ssl',
                    'title' => 'SMTP use SSL',
                    'description' => '',
                    'field_type' => 'checkbox',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '0',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => '0',
                    'default_value' => '0',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'mail_smtp_username',
                    'title' => 'SMTP Username',
                    'description' => '',
                    'field_type' => 'text',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '0',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => '',
                    'default_value' => '',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'mail_transport',
                    'title' => 'Email Transport Method',
                    'description' => '',
                    'field_type' => 'select',
                    'rules' => '',
                    'options' => '{"php":"php","smtp":"smtp","sendmail":"sendmail"}',
                    'placeholder' => '',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => 'php',
                    'default_value' => 'php',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'next_invoice_number',
                    'title' => 'Next Invoice Number',
                    'description' => 'This number will automatically increment as new invoices are created. Once set you should not modify it.',
                    'field_type' => 'text',
                    'rules' => 'integer',
                    'options' => '',
                    'placeholder' => 'e.g 2500',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '2',
                    'value' => '5000',
                    'default_value' => '2500',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'random_password_length',
                    'title' => 'Random Password Length',
                    'description' => 'The default length of system-generated passwords.',
                    'field_type' => 'text',
                    'rules' => 'integer',
                    'options' => '',
                    'placeholder' => 'e.g 20',
                    'setting_category_id' => '1',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => '20',
                    'default_value' => '20',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'results_per_page',
                    'title' => 'Results Per Page',
                    'description' => 'The number of results per page to display on paginated tables.',
                    'field_type' => 'text',
                    'rules' => 'integer|required|min:1',
                    'options' => '',
                    'placeholder' => 'e.g 15',
                    'setting_category_id' => '1',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => '15',
                    'default_value' => '15',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'send_emails_from',
                    'title' => 'Default Email From Address',
                    'description' => '',
                    'field_type' => 'text',
                    'rules' => 'email',
                    'options' => '',
                    'placeholder' => 'e.g no-reply@example.com',
                    'setting_category_id' => '3',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => 'NO-REPLY@example.com',
                    'default_value' => 'NO-REPLY@example.com',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'short_datetime_format',
                    'title' => 'Short DateTime Format',
                    'description' => '',
                    'field_type' => 'text',
                    'rules' => 'required',
                    'options' => '',
                    'placeholder' => 'e.g m/d/y, G:i:s',
                    'setting_category_id' => '4',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => 'm/d/y, G:i:s',
                    'default_value' => 'm/d/y, G:i:s',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'short_date_format',
                    'title' => 'Short Date Format',
                    'description' => 'The format for short dates, without the time being shown.',
                    'field_type' => 'text',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => 'e.g m/d/y',
                    'setting_category_id' => '4',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => 'm/d/y',
                    'default_value' => 'm/d/y',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'timezone',
                    'title' => 'System Timezone',
                    'description' => '',
                    'field_type' => 'text',
                    'rules' => 'required',
                    'options' => '',
                    'placeholder' => 'e.g America/New_York',
                    'setting_category_id' => '4',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '0',
                    'value' => 'Europe/London',
                    'default_value' => 'America/New_York',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'invoice_days',
                    'title' => 'Invoice Days Before Renewal',
                    'description' => 'How many days in advance an invoice for a standard product is generated',
                    'field_type' => 'text',
                    'rules' => 'integer|min:1',
                    'options' => '',
                    'placeholder' => 'e.g 30',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '2',
                    'value' => '30',
                    'default_value' => '30',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'first_overdue_notice_days',
                    'title' => 'First Overdue Notice',
                    'description' => 'The number of days after an invoice becomes overdue to send out a reminder. Set to zero to disable reminder.',
                    'field_type' => 'text',
                    'rules' => 'integer|min:0',
                    'options' => '',
                    'placeholder' => 'e.g 3',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '5',
                    'value' => '3',
                    'default_value' => '3',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'second_overdue_notice_days',
                    'title' => 'Second Overdue Notice',
                    'description' => 'The number of days after an invoice becomes overdue to send out a second reminder. Set to zero to disable reminder.',
                    'field_type' => 'text',
                    'rules' => 'integer|min:0',
                    'options' => '',
                    'placeholder' => 'e.g 6',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '6',
                    'value' => '6',
                    'default_value' => '6',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'third_overdue_notice_days',
                    'title' => 'Third Overdue Notice',
                    'description' => 'The number of days after an invoice becomes overdue to send out a third and final reminder. Set to zero to disable reminder.',
                    'field_type' => 'text',
                    'rules' => 'integer|min:0',
                    'options' => '',
                    'placeholder' => 'e.g 9',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '7',
                    'value' => '9',
                    'default_value' => '9',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'next_order_number',
                    'title' => 'Next Order Number',
                    'description' => 'This number will automatically increment as new orders are created. Once set you should not modify it.',
                    'field_type' => 'text',
                    'rules' => 'integer',
                    'options' => '',
                    'placeholder' => 'e.g 2500',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '3',
                    'value' => '2500',
                    'default_value' => '2500',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'welcometext_header',
                    'title' => 'Welcome Text Header',
                    'description' => 'The header shown on the welcome text block.',
                    'field_type' => 'text',
                    'rules' => '',
                    'options' => 'max:100',
                    'placeholder' => '',
                    'setting_category_id' => '6',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '1',
                    'value' => 'Welcome!',
                    'default_value' => 'Welcome!',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'welcometext_body',
                    'title' => 'Welcome Text Body',
                    'description' => 'The message shown in the welcome text block.',
                    'field_type' => 'textarea',
                    'rules' => '',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '6',
                    'editable' => '1',
                    'required' => '1',
                    'addon_id' => '',
                    'sort' => '2',
                    'value' => '<p>Welcome to the client billing and support area of SiteName.com. This is the default welcome message that ships with WHSuite. You can change it from the frontend settings section, located in the admin area.</p>',
                    'default_value' => '<p>Welcome to the client billing and support area of SiteName.com. This is the default welcome message that ships with WHSuite. You can change it from the frontend settings section, located in the admin area.</p>',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'enable_credit_card_payments',
                    'title' => 'Enable Credit Card Payments',
                    'description' => '',
                    'field_type' => 'checkbox',
                    'rules' => 'min:0|max:1',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '0',
                    'addon_id' => '0',
                    'sort' => '8',
                    'value' => '1',
                    'default_value' => '0',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'enable_ach_payments',
                    'title' => 'Enable ACH Payments',
                    'description' => '',
                    'field_type' => 'checkbox',
                    'rules' => 'min:0|max:1',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '0',
                    'addon_id' => '0',
                    'sort' => '9',
                    'value' => '1',
                    'default_value' => '',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'store_credit_cards',
                    'title' => 'Enable Credit Card Storage',
                    'description' => '',
                    'field_type' => 'checkbox',
                    'rules' => 'min:0|max:1',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '0',
                    'addon_id' => '0',
                    'sort' => '10',
                    'value' => '1',
                    'default_value' => '0',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'slug' => 'store_ach',
                    'title' => 'Enable ACH Storage',
                    'description' => '',
                    'field_type' => 'checkbox',
                    'rules' => 'min:0|max:1',
                    'options' => '',
                    'placeholder' => '',
                    'setting_category_id' => '2',
                    'editable' => '1',
                    'required' => '0',
                    'addon_id' => '0',
                    'sort' => '11',
                    'value' => '1',
                    'default_value' => '0',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // insert shortcuts
        \Shortcut::insert(
            array(
                array(
                    'unique_name' => 'reports',
                    'addon_id' => null,
                    'name' => 'reports',
                    'icon_class' => 'fa fa-bar-chart-o',
                    'description' => 'Provides a shortcut to the current pending orders',
                    'route' => 'admin-reports',
                    'label_route' => null,
                    'is_active' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'unique_name' => 'overdue_invoices',
                    'addon_id' => null,
                    'name' => 'shortcut_overdue_invoices',
                    'icon_class' => 'fa fa-exclamation-triangle',
                    'description' => 'Provides a shortcut to overdue invoices',
                    'route' => 'admin-invoice',
                    'label_route' => 'admin-shortcut-invoices-overdue',
                    'is_active' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'unique_name' => 'unpaid_invoices',
                    'addon_id' => null,
                    'name' => 'shortcut_unpaid_invoices',
                    'icon_class' => 'fa fa-money',
                    'description' => 'Provides a shortcut to all the unpaid invoices',
                    'route' => 'admin-invoice',
                    'label_route' => 'admin-shortcut-invoices-unpaid',
                    'is_active' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'unique_name' => 'new_customers',
                    'addon_id' => null,
                    'name' => 'shortcut_new_customers',
                    'icon_class' => 'fa fa-group',
                    'description' => 'Provides a shortcut to all new customers',
                    'route' => 'admin-client',
                    'label_route' => 'admin-shortcut-clients-new',
                    'is_active' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ),
                array(
                    'unique_name' => 'new_orders',
                    'addon_id' => null,
                    'name' => 'shortcut_new_orders',
                    'icon_class' => 'fa fa-shopping-cart',
                    'description' => 'Provides a shortcut to all the new orders',
                    'route' => 'admin-order',
                    'label_route' => 'admin-shortcut-orders-new',
                    'is_active' => 1,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // insert default staff groups
        \StaffGroup::insert(
            array(
                array(
                    'name' => 'Admin',
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                )
            )
        );

        // insert defaults widget
        \Widget::insert(
            array(
                array(
                    'unique_name' => 'orders-recent-orders',
                    'addon_id' => null,
                    'name' => 'widget_recent_orders',
                    'description' => 'Show the latest orders on your site',
                    'route' => 'admin-widget-orders-recent-orders',
                    'is_active' => 1,
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
