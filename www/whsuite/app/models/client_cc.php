<?php

class ClientCc extends AppModel
{
    public $table = 'client_cc';

    public static $rules = array(
        'first_name' => 'required|max:150',
        'last_name' => 'required|max:150',
        'address1' => 'max:150|required',
        'address2' => 'max:150',
        'city' => 'max:150|required',
        'state' => 'max:150|required',
        'postcode' => 'max:50|required',
        'country' => 'max:255|required',
        'account_number' => 'max:19',
        'account_expiry' => 'max:4',
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

    public static function getCcs($client_id, $currency_id = false)
    {
        $cards = self::where('client_id', '=', $client_id)
            ->with('Currency');

        if ($currency_id !== false && $currency_id > 0) {

            $cards = $cards->where('currency_id', '=', $currency_id);
        }

        $cards = $cards->get();

        if ($cards->count() < 1) {
            return null;
        }

        foreach($cards as &$card) {
            $card->account_last4 = App::get('security')->decrypt($card->account_last4);
            $card->account_expiry = App::get('security')->decrypt($card->account_expiry);
            $card->account_number = '************'.$card->account_last4;
        }

        return $cards;
    }

    public static function getCc($cc_id)
    {
        $card = self::where('id', '=', $cc_id)
            ->with('Currency')
            ->first();

        if ($card->count() < 1) {
            return null;
        }

        $card->account_last4 = App::get('security')->decrypt($card->account_last4);
        $card->account_expiry = App::get('security')->decrypt($card->account_expiry);
        $card->account_number = '************'.$card->account_last4;

        return $card;
    }

    public static function saveCc($data, $cc_id = 0, $client_id)
    {
        if (is_null($cc = ClientCc::find($cc_id))) {
            $cc = new ClientCc;
        }

        $cc->client_id = $client_id;
        $cc->first_name = (isset($data['first_name'])) ? $data['first_name'] : null;
        $cc->last_name = (isset($data['last_name'])) ? $data['last_name'] : null;
        $cc->company = (isset($data['company'])) ? $data['company'] : null;
        $cc->customer_type = (! empty($cc->company)) ? self::$customer_types['business'] : self::$customer_types['individual'];
        $cc->email = (isset($data['email'])) ? $data['email'] : null;
        $cc->address1 = (isset($data['address1'])) ? $data['address1'] : null;
        $cc->address2 = (isset($data['address2'])) ? $data['address2'] : null;
        $cc->city = (isset($data['city'])) ? $data['city'] : null;
        $cc->state = (isset($data['state'])) ? $data['state'] : null;
        $cc->postcode = (isset($data['postcode'])) ? $data['postcode'] : null;
        $cc->country = (isset($data['country'])) ? $data['country'] : null;
        $cc->is_active = (isset($data['is_active'])) ? $data['is_active'] : 0;
        $cc->is_default = (isset($data['is_default'])) ? $data['is_default'] : 0;
        $cc->account_expiry = (isset($data['account_expiry'])) ? $data['account_expiry'] : 0;

        // we only want to set the currency id on an add,
        // so if it's already set ignore it
        if (empty($cc->currency_id)) {

            $cc->currency_id = (isset($data['currency_id'])) ? $data['currency_id'] : 0;
        }

        if (
            $data['account_number'] == '' ||
            strpos($data['account_number'], '**') !== false ||
            ! self::validateCc($data['account_number'])
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
        $gateway_link = GatewayCurrency::where('currency_id', '=', $cc->currency_id)
            ->orderBy('sort', 'asc')
            ->first();

        $offsite = false;

        if (strpos($data['account_number'], '**') === false && !empty($gateway_link)) {

            // We found a record, now lets get the gateway
            $gateway = Gateway::find($gateway_link->gateway_id);

            if (
                ! empty($gateway) &&
                $gateway->is_merchant == '1' &&
                $gateway->store_cc == 1 &&
                App::get('configs')->get('settings.billing.store_credit_cards') == 1
            ) {

                // We found the gateway, and it's a merchant so we're good to go!
                // Load up the merchant gateway details
                $addon = $gateway->addon()->first();

                // The gateway supports Cc storage, so lets go ahead and
                // attempt to store the data.

                App::get('hooks')->callListeners('update-client-cc', $client_id, $cc, $data);

                if (class_exists('Addon\\'.ucfirst($addon->directory).'\Libraries\\'.$addon->directory.'Cc')) {

                    $cc->account_number = self::cleanCc($data['account_number']);

                    $cc->gateway_data = App::factory('Addon\\'.ucfirst($addon->directory).'\Libraries\\'.$addon->directory.'Cc')
                        ->saveCc(
                            $client->id,
                            $cc->toArray(),
                            $cc_id
                        );
                    $cc->gateway_id = $gateway->id;

                    $offsite = true;

                    $cc->account_number = App::get('security')->rsaEncrypt('ACCOUNT STORED OFFSITE');

                } else {
                    App::get('session')->setFlash('error', $this->lang->get('gateway_does_not_support_offsite_storage'));
                    return null;
                }

            }
        }

        if (! $offsite) {
            if (strpos($data['account_number'], '**') === false) {
                $cc->account_number = App::get('security')->rsaEncrypt(self::cleanCc($data['account_number']));
            }
        }

        if (strpos($data['account_number'], '**') === false) {
            $cc->account_last4 = App::get('security')->encrypt(substr($data['account_number'], -4));
            $cc->account_type = self::ccType($data['account_number']);
        }


        $cc->account_expiry = App::get('security')->encrypt($cc->account_expiry);

        $saved = $cc->save();
        if ($cc->is_default == '1') {
            ClientCc::setDefault($cc->id, $client_id);
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

            App::factory('Addon\\'.ucfirst($addon->directory).'\Libraries\\'.$addon->directory.'Cc')->deleteCc($this->toArray());
        }
        return parent::delete();
    }

    public static function setDefault($cc_id, $client_id)
    {
        $ccs = ClientCc::where('client_id', '=', $client_id)->get();
        foreach($ccs as $cc)
        {
            if ($cc->id == $cc_id) {
                $cc->is_default = '1';
                $cc->save();
            } else {
                $cc->is_default = 0;
                $cc->save();
            }
        }
    }

    public static function cleanCc($number)
    {
        return preg_replace('/\D/', '', $number);
    }

    public static function ccType($number)
    {
        $card_types = array(
            'mastercard' => array(
                'pattern' => "/^(5[1-5][0-9]{14})$/",
                'lengths' => array(16)
            ),
            'visa electron' => array(
                'pattern' => "/^(4026|417500|4508|4844|491(3|7))/",
                'lengths' => array(16)
            ),
            'visa' => array(
                'pattern' => "/^([4]{1})([0-9]{12,15})$/",
                'lengths' => array(16)
            ),
            'amex' => array(
                'pattern' => "/^([34|37]{2})([0-9]{13})$/",
                'lengths' => array(15)
            ),
            'discover' => array(
                'pattern' => "/^([6011]{4})([0-9]{12})$/",
                'lengths' => array(16)
            ),
            'diners' => array(
                'pattern' => "/^([30|36|38]{2})([0-9]{12})$/",
                'lengths' => array(14)
            ),
            'enroute' => array(
                'pattern' => "/^(^(2014)|^(2149))\d{11}$/",
                'lengths' => array(16)
            ),
            'jcb' => array(
                'pattern' => "/^(3[0-9]{15}|(2131|1800)[0-9]{11})$/",
                'lengths' => array(15,16)
            ),
            'laser' => array(
                'pattern' => "/^(6304|670[69]|6771)/",
                'lengths' => array(16,17,18,19)
            ),
            'maestro' => array(
                'pattern' => "/^(5018|5020|5038|6304|6759|676[1-3])/",
                'lengths' => array(12,13,14,15,16,17,18,19)
            ),
        );

        foreach ($card_types as $type => $options) {
            if (preg_match($options['pattern'], $number) && in_array(strlen($number), $options['lengths'])) {
                return $type;
            }
        }

        return 'issuer unknown';
    }

    public static function validateCc($number)
    {
        $number = preg_replace('/\D/', '', $number);
        $length = strlen($number);
        $parity=$length % 2;

        $total = 0;
        for ($i=0;$i<$length;$i++) {
            $digit = $number[$i];

            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $total += $digit;
        }
        return ($total % 10 == 0) ? TRUE : FALSE;
    }

}
