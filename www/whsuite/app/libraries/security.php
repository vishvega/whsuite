<?php

namespace App\Libraries;

class Security
{
    private $crypt;

    public function rsaEncrypt($string)
    {
        $rsa = new \Crypt_RSA();
        $public_key = \App::get('configs')->get('settings.sys_public_key');
        $rsa->loadKey($public_key);

        return base64_encode($rsa->encrypt($this->encrypt($string)));
    }

    public function rsaDecrypt($string, $passphrase = null)
    {
        $rsa = new \Crypt_RSA();

        if (!is_null($passphrase)) {
            $rsa->setPassword($passphrase);
        }

        $private_key = $this->decrypt(\App::get('configs')->get('settings.sys_private_key'));
        $rsa->loadKey($private_key);
        $rsa_decrypted = $rsa->decrypt(base64_decode($string));

        if (empty($rsa_decrypted)) {
            return false;
        } else {
            return $this->decrypt($rsa_decrypted);
        }
    }

    public function rsaSetPassphrase($old_passphrase = null, $passphrase)
    {
        $rsa = new \Crypt_RSA();

        if (!is_null($old_passphrase)) {
            $rsa->setPassword($old_passphrase);
        }

        $rsa->loadKey($this->decrypt(\App::get('configs')->get('settings.sys_private_key')));

        // Set the new key
        $rsa->setPassword($passphrase);

        $private_key = $rsa->getPrivateKey();

        $private_key_row = \Setting::where('slug', '=', 'sys_private_key')->first();
        $private_key_row->value = $this->encrypt($private_key);
        $private_key_row->save();

        $passphrase_row = \Setting::where('slug', '=', 'sys_private_key_passphrase')->first();
        $passphrase_row->value = $this->hash($passphrase);
        $passphrase_row->save();
    }

    public function encrypt($string, $iv = true)
    {
        $aes = $this->initAES('CTR');
        $aes->setKey($this->hash(\App::get('configs')->get('security.encryption_key')));

        if ($iv) {
            // The IV is wanted, so first lets create the vector.
            $vector = $this->generateIV();
            $aes->setIV($vector);
        }

        $string = $aes->encrypt($string);

        if (isset($vector)) {
            $newString = $string.'|+W.'.$vector;
            $string = $this->encrypt($newString, false);
        }
        return base64_encode($string);
    }

    public function decrypt($string, $iv = true)
    {
        if (empty($string)) {
            return $string;
        }

        $aes = $this->initAES('CTR');
        $aes->setKey($this->hash(\App::get('configs')->get('security.encryption_key')));

        $string = base64_decode($string);

        if ($iv) {
            $string = $this->decrypt($string, false);

            $data = explode('|+W.', $string, 2);

            $aes->setIv($data[1]);
            $string = $data[0];
        }

        return $aes->decrypt($string);
    }

    public function hash($string)
    {
        $crypt = new \Crypt_Hash('sha256');
        $crypt->setKey(\App::get('configs')->get('security.encryption_key'));

        return bin2hex($crypt->hash($string));
    }

    public function requestData($type, $user, $model, $id, $column)
    {
        $authenticated = false;
        $passphrase = null;

        $post_data = \Whsuite\Inputs\Post::get();

        if (isset($post_data['password'])) {
            $password = $post_data['password'];

            $data = $model::find($id);
            $data = $data->$column;
            if ($type == 'rsa') {
                if ($this->checkPassphraseAuth()) {
                    // We are using a passphrase
                    $string = $this->rsaDecrypt($data, $password);
                } else {
                    // We are just using a password
                    $string = $this->rsaDecrypt($data);
                }

                $user_type = '';
                if ($user->getTable() == 'staffs') {
                    $user_type = 'staff';
                } elseif ($user->getTable() == 'clients') {
                    $user_type = 'client';
                }

                \Log::logAction($user->id, 'decrypt', 'Decrypt column: '.$column.' from datastore: '.$model.' with primary key: '.$id, $user_type);
            } else {
                // AES
                if ($user->checkPassword($password)) {
                    // Password correct, decrypt the data.

                    $string = $this->decrypt($data);
                } else {
                    die('Invalid Password');
                }
            }

            if ($string != '') {
                \App::get('view')->set('string', $string);
                \App::get('view')->display('security/decryptedDataPopup.php');
            } else {
                \App::get('view')->set('message_body', \App::get('translation')->get('decrypt_error'));
                \App::get('view')->display('elements/messages/fail.php');
            }

        } else {
            die('Invalid Access');
        }
    }

    public function checkPassphraseAuth()
    {
        if (\App::get('configs')->get('settings.sys_private_key_passphrase') !='') {
            return true;
        }
        return false;
    }

    private function initAES($mode = null)
    {
        if ($mode == 'CTR') {
            $mode = 'CRYPT_AES_MODE_CTR';
        } elseif ($mode == 'CFB') {
            $mode = 'CRYPT_AES_MODE_CFB';
        } elseif ($mode == 'OFB') {
            $mode = 'CRYPT_AES_MODE_OFB';
        } else {
            $mode = 'CRYPT_AES_MODE_CBC';
        }
        return new \Crypt_AES($mode);
    }

    protected function generateIV($length = null)
    {
        if (! $length) {
            $length = \App::get('configs')->get('security.encryption_vector_size');
        }

        if (defined('MCRYPT_DEV_URANDOM')) {
            return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        }

        if (defined('MCRYPT_DEV_RANDOM')) {
            return mcrypt_create_iv($length, MCRYPT_DEV_RANDOM);
        }

        mt_srand();
        return mcrypt_create_iv($length, MCRYPT_RAND);
    }
}
