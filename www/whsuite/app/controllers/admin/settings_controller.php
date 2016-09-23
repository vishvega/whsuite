<?php

use \Whsuite\Inputs\Post as PostInput;

/**
 * Admin Settings Controller
 *
 * The settings controller handles the admin configuration of system settings.
 * The settings are shown in categories to make things easier for the end-user to
 * edit.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class SettingsController extends AdminController
{
    /*
     * View Settings Group
     *
     * @param id int The ID of the settings category to load (defaults to 1)
     */
    public function viewCategory($id = 1)
    {
        $category = SettingCategory::find($id);
        if (empty($category)) {
            return $this->redirect('admin-home');
        }

        $settings = $category->Setting()->orderByRaw('sort = 0, sort ASC')->get();

        if (\Whsuite\Inputs\Post::get()) {
            $post_data = \Whsuite\Inputs\Post::get('Setting');

            // On the first loop through, we're only wanting to validate the settings
            // and put together a list of errors.
            $validation_errors = array();
            foreach ($settings as $setting) {
                if ($setting->rules || $setting->required) {
                    $setting_rules = $setting->rules;

                    if ($setting->required) {
                        if (strlen($setting_rules) > 0) {
                            $setting_rules .='|required';
                        } else {
                            $setting_rules = 'required';
                        }
                    }

                    $rules = array('value' => $setting_rules);
                    $setting_validator = $this->validator->make($post_data[$setting->slug], $rules);

                    if ($setting_validator->fails()) {
                        $message = $setting_validator->messages()->toArray();
                        $validation_errors[$setting->title] = $message['value'];
                    }
                }
                $setting->value = $post_data[$setting->slug]['value']; // We still set the value as we'll want to display the last entered value.
            }

            if (empty($validation_errors)) {
                // No errors, so go ahead and update the settings values.
                $save_failures = false;
                foreach ($settings as $setting) {
                    $setting->value = $post_data[$setting->slug]['value'];
                    if (!$setting->save()) {
                        $save_failures = true;
                    }
                }

                if (! $save_failures) {
                    // Purge the translations cache
                    $this->lang->purge();

                    // All the records were saved successfully.
                    App::get('session')->setFlash('success', $this->lang->get('settings_updated'));
                } else {
                    // All the records were saved successfully.
                    App::get('session')->setFlash('error', $this->lang->get('an_error_occurred'));
                }

                return $this->redirect('admin-settings-category', ['id' => $category->id]);
            } else {
                \App\Libraries\Message::set($this->lang->formatErrors(json_encode($validation_errors)), 'fail');
            }
        }

        // Create the array of values needed for populating the form
        $setting_values = array();
        foreach ($settings as $setting) {
            $setting_values[$setting->slug]['value'] = $setting->value;
        }

        \Whsuite\Inputs\Post::set('Setting', $setting_values);

        $title = $this->lang->get($category->title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('system_settings'), 'admin-settings');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        if (DEV_MODE == 'true') {
            $setting_categories = SettingCategory::orderByRaw('sort = 0, sort ASC')->get();
        } else {
            $setting_categories = SettingCategory::where('is_visible', '=', '1')->get();
        }

        $this->view->set('title', $title);
        $this->view->set('settings', $settings);
        $this->view->set('category', $category);
        $this->view->set('settings_categories', $setting_categories);
        $this->view->display('settings/viewCategory.php');
    }

    public function passphraseSettings()
    {
        $passphrase_set = false;
        $passphrase = App::get('configs')->get('settings.sys_private_key_passphrase');

        if ($passphrase != '') {
            $passphrase_set = true;
        }

        if (\Whsuite\Inputs\Post::get()) {
            $new_passphrase = \Whsuite\Inputs\Post::get('passphrase');
            $confirm_passphrase = \Whsuite\Inputs\Post::get('confirm_passphrase');

            if ($new_passphrase != $confirm_passphrase) {
                \App\Libraries\Message::set($this->lang->get('passphrase_not_match'), 'fail');
            } else {
                if ($passphrase_set) {
                    $current_passphrase = \Whsuite\Inputs\Post::get('current_passphrase');

                    // Verify that the passphrase is valid.
                    $encrypted = App::get('security')->rsaEncrypt('passphrase check');
                    $decrypted = App::get('security')->rsaDecrypt($encrypted, $current_passphrase);

                    if ($decrypted == 'passphrase check') {
                        // The existing passphrase was entered correctly.
                        App::get('security')->rsaSetPassphrase($current_passphrase, $new_passphrase);
                        \App\Libraries\Message::set($this->lang->get('passphrase_updated'), 'success');
                    } else {
                        // The existing passphrase was not valid.
                        \App\Libraries\Message::set($this->lang->get('existing_passphrase_invalid'), 'fail');
                    }
                } else {
                    App::get('security')->rsaSetPassphrase(null, $new_passphrase);
                    \App\Libraries\Message::set($this->lang->get('passphrase_updated'), 'success');
                }
            }
        }

        if (DEV_MODE == 'true') {
            $setting_categories = SettingCategory::orderByRaw('sort = 0, sort ASC')->get();
        } else {
            $setting_categories = SettingCategory::where('is_visible', '=', '1')->get();
        }

        $title = $this->lang->get('passphrase_settings');

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('system_settings'), 'admin-settings');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('settings_categories', $setting_categories);
        $this->view->set('disable_messages', true); // Disables the default location of the flash messages
        $this->view->set('passphrase_set', $passphrase_set);
        $this->view->display('settings/passphraseSettings.php');
    }
}
