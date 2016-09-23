<?php

namespace App\Libraries;

class Message
{
    const PROTECT = true; // Protect the view var

    public static function set($message_body, $type = 'success', $protect = false)
    {
        if ($type == 'fail') {
            $template = 'elements/messages/fail.php';
        } elseif ($type == 'warning') {
            $template = 'elements/messages/warning.php';
        } elseif ($type == 'info') {
            $template = 'elements/messages/info.php';
        } else {
            $template = 'elements/messages/success.php';
        }

        \App::get('view')->set('message_body', $message_body);
        $message = \App::get('view')->fetch($template);
        \App::get('view')->set('message', $message);

        if ($protect) {
            \App::get('view')->protectVar('message');
        }

        return;
    }
}
