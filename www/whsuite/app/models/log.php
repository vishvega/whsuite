<?php

class Log extends AppModel
{
    public static function logAction($user_id, $action_type, $action, $user_type = 'staff')
    {
        $log = new Log();

        if ($user_type == 'staff') {

            $log->staff_id = $user_id;
        } elseif($user_type == 'client') {

            $log->client_id = $user_id;
        }

        $log->action_type = App::get('translation')->get($action_type);
        $log->action = App::get('translation')->get($action);

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {

            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $log->ip_address = $ip;

        return $log->save();
    }


    public function Staff()
    {
        return $this->belongsTo('Staff');
    }

    public function Client()
    {
        return $this->belongsTo('Client');
    }
}
