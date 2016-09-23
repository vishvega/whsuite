<?php
/**
 * Client Model
 *
 * The clients table stores all the main client details.
 *
 * @package  WHSuite-Models
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class Client extends AppSentryUser
{
    protected $table = 'clients';

    protected $custom_fields = true;
    protected $custom_fields_slug = 'client_fields';

    public static $status_types = array(
        '0' => 'inactive',
        '1' => 'active',
        '2' => 'fraud',
        '3' => 'account_closed'
    );

    public static $rules = array(
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'company' => 'max:255',
        'email' => 'email|required',
        'password' => 'same:confirm_password',
        'confirm_password' => '',
        'html_emails' => 'integer|max:1',
        'address1' => 'max:150|required',
        'address2' => 'max:150',
        'city' => 'max:150|required',
        'state' => 'max:150|required',
        'postcode' => 'max:50|required',
        'country' => 'max:255|required',
        'phone' => 'max:25|required',
        'currency_id' => 'integer',
        'status' => 'integer',
        'language_id' => 'integer',
        'is_taxexempt' => 'integer|max:1',
        'first_ip' => 'max:46',
        'first_hostname' => 'max:255',
        'last_ip' => 'max:46',
        'last_hostname' => 'max:255',
        'last_login' => 'max:255',
        'activated' => 'integer|max:1'
    );

    /**
     * scope created to make loading clients that are proper members easier
     * called: $Client::member()->get(); // gets all registered clients
     */

    public function scopeMember($query)
    {
        return $query->where(
            function ($query) {

                return $query->where('guest_account', '=', 0)
                    ->orWhereNull('guest_account');
            }
        );
    }

    public static function all($columns = array('*'))
    {
        $client = parent::all();

        foreach ($client as $id => $s) {
            if ($s['last_login']) {
                $Carbon = \Carbon\Carbon::parse(
                    $s['last_login'],
                    \App::get('configs')->get('settings.localization.timezone')
                );

                $client[$id]['last_login'] = $CarbonRenewal
                    ->format(\App::get('configs')->get('settings.localization.short_datetime_format'));
            } else {
                $client[$id]['last_login'] = App::get('translation')->get('not_available');
            }
        }

        return $client;
    }

    public static function formattedStatusList()
    {
        $types = array();
        foreach (self::$status_types as $id => $type) {

            $types[$id] = App::get('translation')->get($type);
        }

        return $types;
    }

    /**
     * count the clients for the shortcut label
     *
     * @param   datetime  current date to work from, gets clients created in last week
     * @return  int       number of new clients
     */
    public static function countNew($now)
    {
        $week_ago = $now->copy()->subWeek();

        $instance = new static;
        $query = $instance->newQuery();

        return $query->where('created_at', '<=', $now)
            ->where('created_at', '>=', $week_ago)
            ->where('guest_account', '=', 0)
            ->count();
    }

    // slight sentry annoyance.
    // override group relation to allow it to get groups
    public function groups()
    {
        return $this->belongsToMany('ClientGroup');
    }

    // relationship for us to use.
    public function ClientGroup()
    {
        return $this->belongsToMany('ClientGroup');
    }

    public function Currency()
    {
        return $this->belongsTo('Currency');
    }

    public function ClientEmail()
    {
        return $this->hasMany('ClientEmail');
    }

    public function ClientNote()
    {
        return $this->hasMany('ClientNote');
    }

    public function ClientCc()
    {
        return $this->hasMany('ClientCc');
    }

    public function ClientAch()
    {
        return $this->hasMany('ClientAch');
    }

    public function Contact()
    {
        return $this->hasMany('Contact');
    }

    public function Language()
    {
        return $this->belongsTo('Language');
    }

    /**
     * Save the model to the database. If this is a new record, create
     * a random unique hash.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = array())
    {
        if (empty($this->hash) && isset($options['createHash']) && $options['createHash'] === true) {

            if (! \App::check('security')) {
                \App::factory('\App\Libraries\Security');
            }
            $this->hash = \App::get('security')->hash($this);
        }

        return parent::save($options);
    }
}
