<?php

class ClientAch extends AppModel
{
    public $table = 'client_ach';

    public static $rules = array(
        'first_name' => 'required|max:150',
        'last_name' => 'required|max:150',
        'address1' => 'max:150|required',
        'address2' => 'max:150',
        'city' => 'max:150|required',
        'state' => 'max:150|required',
        'postcode' => 'max:50|required',
        'country' => 'max:255|required',
        'account_type' => 'required',
        'account_number' => 'required',
        'account_routing_number' => 'required',
        'account_last4' => 'max:4',
        'gateway_id' => 'integer',
        'currency_id' => 'integer',
        'is_default' => 'max:1|min:0|integer',
        'is_active' => 'max:1|min:0|integer',
    );

    public static $customer_types = array(
        'individual' => 'individual',
        'business' => 'business'
    );

    public function Client()
    {
        return $this->belongsTo('Client');
    }

    public function Gateway()
    {
        return $this->belongsTo('Gateway');
    }

    public function Currency()
    {
        return $this->belongsTo('Currency');
    }

    public static function getAchs($client_id, $currency_id = false)
    {
        $achs = ClientAch::where('client_id', '=', $client_id)
            ->with('Currency');

        if ($currency_id !== false && $currency_id > 0) {

            $achs = $achs->where('currency_id', '=', $currency_id);
        }

        $achs = $achs->get();

        if ($achs->count() < 1) {
            return null;
        }

        foreach ($achs as &$ach) {
            $ach->account_last4 = App::get('security')->decrypt($ach->account_last4);
            $ach->account_number = '****'.$ach->account_last4;
            $ach->account_routing_number = '*********';
        }

        return $achs;
    }

    public static function getAch($ach_id)
    {
        $ach = ClientAch::where('id', '=', $ach_id)
            ->with('Currency')
            ->first();

        if ($ach->count() < 1) {
            return null;
        }

        $ach->account_last4 = App::get('security')->decrypt($ach->account_last4);
        $ach->account_number = '****'.$ach->account_last4;
        $ach->account_routing_number = '*********';

        return $ach;
    }

    public static function saveAch($data, $ach_id = 0, $client_id)
    {
        if (is_null($ach = ClientAch::find($ach_id))) {
            $ach = new ClientAch;
        }

        $ach->client_id = $client_id;
        $ach->first_name = (isset($data['first_name'])) ? $data['first_name'] : null;
        $ach->last_name = (isset($data['last_name'])) ? $data['last_name'] : null;
        $ach->email = (isset($data['email'])) ? $data['email'] : null;
        $ach->company = (isset($data['company'])) ? $data['company'] : null;
        $ach->customer_type = (! empty($ach->company)) ? self::$customer_types['business'] : self::$customer_types['individual'];
        $ach->address1 = (isset($data['address1'])) ? $data['address1'] : null;
        $ach->address2 = (isset($data['address2'])) ? $data['address2'] : null;
        $ach->city = (isset($data['city'])) ? $data['city'] : null;
        $ach->state = (isset($data['state'])) ? $data['state'] : null;
        $ach->postcode = (isset($data['postcode'])) ? $data['postcode'] : null;
        $ach->country = (isset($data['country'])) ? $data['country'] : null;
        $ach->is_active = (isset($data['is_active'])) ? $data['is_active'] : 0;
        $ach->is_default = (isset($data['is_default'])) ? $data['is_default'] : 0;
        $ach->account_type = (isset($data['account_type'])) ? $data['account_type'] : 0;

        // we only want to set the currency id on an add,
        // so if it's already set ignore it
        if (empty($ach->currency_id)) {

            $ach->currency_id = (isset($data['currency_id'])) ? $data['currency_id'] : 0;
        }

        if (
            $data['account_number'] == '' ||
            $data['account_routing_number'] == '' ||
            strpos($data['account_number'], '**') !== false ||
            strpos($data['account_routing_number'], '**') !== false ||
            ! self::validateAch($data['account_number'])
        ) {
            return false;
        }

        // We need to now work out if this card is to be stored on-site or off-site.
        // If its being stored off-site all we keep here is the address/contact info
        // as that's pretty standard information. To do the check we get the client's
        // default currency, and then find the merchant gateway that handles payments
        // for that currency (assuming one is set). If we find one, and it wants to
        // store the cards offsite, we go ahead and do so. We then save a unique
        // identifier from the merchant to match their record with ours.
        $client = Client::find($client_id);
        $gateway_link = GatewayCurrency::where('currency_id', '=', $ach->currency_id)
            ->orderBy('sort', 'asc')
            ->first();

        $offsite = false;

        if (strpos($data['account_number'], '**') === false && !empty($gateway_link)) {

            // We found a record, now lets get the gateway
            $gateway = Gateway::find($gateway_link->gateway_id);
            if (
                ! empty($gateway) &&
                $gateway->is_merchant == '1' &&
                $gateway->store_ach == 1 &&
                App::get('configs')->get('settings.billing.store_ach') == 1
            ) {

                // We found the gateway, and it's a merchant so we're good to go!
                // Load up the merchant gateway details
                $addon = $gateway->addon()->first();

                // The gateway supports ACH storage, so lets go ahead and
                // attempt to store the data.

                App::get('hooks')->callListeners('update-client-ach', $client_id, $ach, $data);

                if (class_exists('Addon\\'.ucfirst($addon->directory).'\Libraries\\'.$addon->directory.'Ach')) {

                    $ach->account_number = self::cleanAch($data['account_number']);
                    $ach->account_routing_number = self::cleanAch($data['account_routing_number']);

                    $ach->gateway_data = App::factory('Addon\\'.ucfirst($addon->directory).'\Libraries\\'.$addon->directory.'Ach')
                        ->saveAch(
                            $client->id,
                            $ach->toArray(),
                            $ach_id
                        );
                    $ach->gateway_id = $gateway->id;

                    $offsite = true;

                    $ach->account_number = App::get('security')->rsaEncrypt('ACCOUNT STORED OFFSITE');
                    $ach->account_routing_number = App::get('security')->rsaEncrypt('ACCOUNT STORED OFFSITE');

                } else {
                    App::get('session')->setFlash('error', $this->lang->get('gateway_does_not_support_offsite_storage'));
                    return null;
                }
            }
        }

        if (! $offsite) {

            if (strpos($data['account_number'], '**') === false) {
                $ach->account_number = App::get('security')->rsaEncrypt(self::cleanAch($data['account_number']));
            }

            if (strpos($data['account_routing_number'], '**') === false) {
                $ach->account_routing_number = App::get('security')->rsaEncrypt(self::cleanAch($data['account_routing_number']));
            }

        }

        if (strpos($data['account_number'], '**') === false) {
            $ach->account_last4 = App::get('security')->encrypt(substr($data['account_number'], -4));
        }

        $saved = $ach->save();
        if ($ach->is_default == '1') {
            ClientAch::setDefault($ach->id, $client_id);
        }
        return $saved;
    }

    public function delete()
    {
        if ($this->gateway_data != '') {
            // The card is stored at the gateway so we need to run the gateways
            // delete option to remove it remotely

            // Attempt to load in the gateway
            $gateway = Gateway::find($this->gateway_id);

            if (!$gateway) {
                return false;
            }
            $addon = $gateway->addon()->first();

            App::factory('Addon\\'.ucfirst($addon->directory).'\Libraries\\'.$addon->directory.'Ach')->deleteAch($this->toArray());
        }

        return parent::delete();
    }

    public static function setDefault($ach_id, $client_id)
    {
        $achs = ClientAch::where('client_id', '=', $client_id)->get();

        foreach ($achs as $ach) {

            if ($ach->id == $ach_id) {

                $ach->is_default == '1';
                $ach->save();
            } else {

                $ach->is_default = 0;
                $ach->save();
            }
        }
    }

    public static function validateAch($number)
    {
        return true;
    }

    public static function cleanAch($number)
    {
        return preg_replace('/\D/', '', $number);
    }

    public static function accountTypes()
    {
        return array(
            'checking' => App::get('translation')->get('checking'),
            'savings' => App::get('translation')->get('savings')
        );
    }
}
