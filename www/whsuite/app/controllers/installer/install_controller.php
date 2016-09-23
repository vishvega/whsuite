<?php

use \Illuminate\Database\Capsule\Manager as Capsule;
use \App\Installer\Exceptions\AlreadyInstalledException;
use \App\Installer\Exceptions\StrictModeEnabledException;

class InstallController extends InstallerController
{
    protected $template = '';

    public function onLoad()
    {
        parent::onLoad();
        $this->view->set('title', 'WHSuite Installer');

        // Check if WHSuite is already installed.
        if ($this->filesystem->exists(STORAGE_DIR . DS . 'whsuite.installed') && App::get('dispatcher')->getRoute()->values['action'] !== 'finish') {
            // Open the file to retrieve the version details

            $installed_version = null;
            foreach ($this->finder->files()->in(STORAGE_DIR . DS)->name('whsuite.installed') as $file) {
                // Set the file contents.
                $installed_version = $file->getContents();
            }

            if (is_null($installed_version)) {
                // No version info found.
                $this->view->set('error', 'A WHSuite installation was found, but the version could not be detected. Please contact support for assistance or re-install WHSuite.');
            } elseif ($installed_version == '1') {
                $installed_version = '1.0.0-rc.1';
            }

            $installed_version = $this->parser->parse($installed_version);
            $new_version = $this->parser->parse($this->new_version);

            // Check if the installed version is older than this version
            if ($this->compare->greaterThan($new_version, $installed_version)) {
                // This installer is for a new version! Let's allow an upgrade
                $title = 'WHSuite Upgrader (' . $installed_version . ' to ' . $new_version . ')';
                $this->template = 'upgradeStepOne.php';
            } else {
                // This is an old installer, or the installer for th current version.
                // We don't want to allow installation to continue as it's likely already
                // been done.
                $home = URL_PREFIX;
                if (empty($home)) {
                    $home = '/';
                }
                return header("Location: " . $home);
            }
        }
    }

    public function extensions()
    {
        if (! isset($title)) {
            $title = 'WHSuite Installer';
        }

        $this->view->set('title', $title);

        // Check that the correct PHP extensions are install
        $extensions = $this->_checkPhpExtensions();
        $this->view->set('extensions', $extensions);

        $trigger_error = false;

        if ($this->missing_required_extensions) {
            $trigger_error = true;
        }

        if (version_compare(PHP_VERSION, $this->min_php_version) < 0) {
            $trigger_error = true;
            $this->view->set('installed_php', PHP_VERSION);
            $this->view->set('required_php', $this->min_php_version);
        } elseif (version_compare(PHP_VERSION, $this->min_recommended_php_version) < 0) {
            $this->view->set('installed_php', PHP_VERSION);
            $this->view->set('recommended_php', $this->min_recommended_php_version);
        }

        if ($trigger_error) {
            // Server requirements were missing. Show our failure view.
            return $this->view->display('missingRequirements.php');
        }

        // Check file/folder permissions
        $required_permissions = array(
            STORAGE_DIR,
            APP_DIR . DS . 'configs'
        );
        $missing_perms = false;
        $failed_permissions = array();

        foreach ($required_permissions as $file) {
            if (! is_writable($file)) {
                $failed_permissions[] = $file;
                $missing_perms = true;
            }
        }

        $this->view->set('failed_permissions', $failed_permissions);
        $this->view->set('missing_perms', $missing_perms);

        if (empty($this->template)) {
            $this->template = 'extensions.php';
        }

        return $this->view->display($this->template);
    }

    public function configureDatabase()
    {
        if (isset($_POST['submit']) && isset($_POST['host']) && isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['name'])) {
            $mysql_host = $_POST['host'];
            $mysql_user = $_POST['user'];
            $mysql_pass = $_POST['pass'];
            $mysql_name = $_POST['name'];
            $mysql_prefix = ''; // We've got a bug in our DB package right now that prevents prefixes working correctly :(

            // Test connection details
            $capsule = new Capsule;
            $capsule->addConnection(array(
                'driver' => 'mysql',
                'host' => $mysql_host,
                'database' => $mysql_name,
                'username' => $mysql_user,
                'password' => $mysql_pass,
                'charset' => 'utf8',
                'collation' => 'utf8_general_ci',
                'prefix' => $mysql_prefix
            ));

            // Setup the Eloquent ORM...
            $capsule->bootEloquent();
            $capsule->setAsGlobal();

            try {
                $capsule->connection();

                if ($this->alreadyInstalled($mysql_name, $mysql_prefix)) {
                    throw new AlreadyInstalledException;
                }

                if ($this->strictModeOn()) {
                    throw new StrictModeEnabledException;
                }

                // Connection details were correct. Create the DB file.
                $db_file_data = "<?php
                    return array(
                        'mysql' => array(
                            'host' => '" . $mysql_host . "',
                            'user' => '" . $mysql_user . "',
                            'pass' => '" . $mysql_pass . "',
                            'name' => '" . $mysql_name . "',
                            'prefix' => '" . $mysql_prefix . "'
                        )
                    );
                ";

                $this->filesystem->dumpFile(APP_DIR . DS . 'configs' . DS . 'database.php', $db_file_data);

                // Nobody will need to edit this now. Give it read-only permissions for the current user.
                $this->filesystem->chmod(APP_DIR . DS . 'configs' . DS . 'database.php', 0644);

                return header("Location: " . \App::get('router')->generate('install-configure'));
            } catch (AlreadyInstalledException $e) {
                $this->view->set('error', 'It looks like you\'ve already got WHSuite setup in this database. If you previously received an error during installation, you will need to delete these tables before trying to reinstall.');
            } catch (StrictModeEnabledException $e) {
                $this->view->set('error', 'It looks like strict mode is enabled in your MySQL / MariaDB server, please disable strict mode before continuing with the installation.');
            } catch (IOExceptionInterface $e) {
                $this->view->set('error', 'An error occurred while creating your directory at ' . $e->getPath());
            } catch (Exception $e) {
                $this->view->set('error', 'There was an error establishing a database connection and/or writing to the database configuration file.');
            }
        }

        // Check file/folder permissions
        $required_permissions = array(
            STORAGE_DIR,
            APP_DIR . DS . 'configs',
            ADDON_DIR . DS
        );
        $missing_perms = false;
        $failed_permissions = array();
        foreach ($required_permissions as $file) {
            if (! is_writable($file)) {
                $failed_permissions[] = $file;
                $missing_perms = true;
            }

        }

        $this->view->set('failed_permissions', $failed_permissions);
        $this->view->set('missing_perms', $missing_perms);

        return $this->view->display('configureDatabase.php');
    }

    public function configureSystem()
    {
        // Check if WHSuite is already installed.
        if ($this->filesystem->exists(STORAGE_DIR . DS . 'whsuite.installed')) {
            $home = URL_PREFIX;

            if (empty($home)) {
                $home = '/';
            }
            return header("Location: " . $home);
        }

        $systemData = ! empty($_POST['site_url']) && ! empty($_POST['site_name']);
        $userData = ! empty($_POST['admin_email']) && ! empty($_POST['admin_password']);

        if (isset($_POST['submit']) && $systemData && $userData) {
            $site_url = filter_var($_POST['site_url'], FILTER_SANITIZE_URL);
            $site_name = filter_var($_POST['site_name'], FILTER_SANITIZE_STRING);
            $admin_email = filter_var($_POST['admin_email'], FILTER_SANITIZE_EMAIL);
            $admin_password = filter_var($_POST['admin_password'], FILTER_SANITIZE_STRING);

            $site_url = preg_replace('{/$}', '', $site_url);

            // Now run all the migrations
            try {
                $migrate = $this->migrations->migrate();
            } catch (\Exception $e) {
                $migrate = false;
            }

            if (is_null($migrate) || $migrate != false) {
                // Migrations completed

                // try to install any language packs
                try {
                    \App\Libraries\LanguageHelper::importAppLanguages();

                    // install required system settings
                    $this->date = \Carbon\Carbon::now();

                    // Setup the system RSA encryption and passphrase service.
                    $security = App::factory('\App\Libraries\Security');

                    $rsa = new \Crypt_RSA();

                    $passphrase = null;
                    $hashed_passphrase = null;

                    extract($rsa->createKey());

                    // Insert the system settings records.
                    $system_settings = \Setting::insert(array(
                        array(
                            'slug' => 'sys_private_key',
                            'title' => 'System Private Key',
                            'description' => null,
                            'field_type' => 'text',
                            'rules' => null,
                            'options' => null,
                            'placeholder' => null,
                            'setting_category_id' => 0,
                            'editable' => 0,
                            'required' => 0,
                            'sort' => 0,
                            'value' => $security->encrypt($privatekey),
                            'created_at' => $this->date,
                            'updated_at' => $this->date
                        ),
                        array(
                            'slug' => 'sys_private_key_passphrase',
                            'title' => 'System Private Key Passphrase',
                            'description' => null,
                            'field_type' => 'text',
                            'rules' => null,
                            'options' => null,
                            'placeholder' => null,
                            'setting_category_id' => 0,
                            'editable' => 0,
                            'required' => 0,
                            'sort' => 0,
                            'value' => $hashed_passphrase,
                            'created_at' => $this->date,
                            'updated_at' => $this->date
                        ),
                        array(
                            'slug' => 'sys_public_key',
                            'title' => 'System Public Key',
                            'description' => null,
                            'field_type' => 'text',
                            'rules' => null,
                            'options' => null,
                            'placeholder' => null,
                            'setting_category_id' => 0,
                            'editable' => 0,
                            'required' => 0,
                            'sort' => 0,
                            'value' => $publickey,
                            'created_at' => $this->date,
                            'updated_at' => $this->date
                        ),
                        array(
                            'slug' => 'site_url',
                            'title' => 'Site URL',
                            'description' => 'The URL of your site',
                            'field_type' => 'text',
                            'rules' => 'url',
                            'options' => null,
                            'placeholder' => 'e.g http://example.com',
                            'setting_category_id' => '1',
                            'editable' => '1',
                            'required' => '1',
                            'sort' => '2',
                            'value' => $site_url,
                            'created_at' => $this->date,
                            'updated_at' => $this->date
                        ),
                        array(
                            'slug' => 'sitename',
                            'title' => 'Site Name',
                            'description' => 'The name of your site',
                            'field_type' => 'text',
                            'rules' => null,
                            'options' => null,
                            'placeholder' => null,
                            'setting_category_id' => '1',
                            'editable' => '1',
                            'required' => '1',
                            'sort' => '1',
                            'value' => $site_name,
                            'created_at' => $this->date,
                            'updated_at' => $this->date
                        ),
                        array(
                            'slug' => 'sitelogo',
                            'title' => 'Site Logo',
                            'description' => 'Path to the site logo, used in certain areas such as payment gateways',
                            'placeholder' => 'e.g http://yoursite.com/logo.png',
                            'field_type' => 'text',
                            'rules' => null,
                            'options' => null,
                            'setting_category_id' => '1',
                            'editable' => '1',
                            'required' => '1',
                            'sort' => '3',
                            'value' => $site_url . '/img/logo.png',
                            'created_at' => $this->date,
                            'updated_at' => $this->date
                        )
                    ));

                    // Create the initial admin user
                    $Staff = new \Staff();

                    $Staff->email = $admin_email;
                    $Staff->password = $admin_password;
                    $Staff->activated = true;
                    $Staff->save();

                    // Add them to the admin staff group
                    $StaffGroup = \StaffGroup::find('1');
                    $Staff->addGroup($StaffGroup);

                    // setup the default shortcuts / widget
                    $Staff->Shortcut()->sync(
                        array(
                            '1' => array(
                                'sort' => 1
                            ),
                            '2' => array(
                                'sort' => 2
                            ),
                            '3' => array(
                                'sort' => 3
                            ),
                            '4' => array(
                                'sort' => 4
                            ),
                            '5' => array(
                                'sort' => 5
                            )
                        )
                    );

                    $Staff->Widget()->sync(
                        array(
                            '1' => array(
                                'sort' => 1
                            )
                        )
                    );

                    $settings = Setting::get();

                    if ($settings->count() > 0) {
                        // Add installation lock file
                        $this->filesystem->dumpFile(STORAGE_DIR . DS . 'whsuite.installed', INSTALL_VERSION);

                        return header("Location: " . \App::get('router')->generate('install-finish'));
                    } else {
                        $this->view->set('error', 'There was a problem setting up the database. Please check that your database user has the correct permissions, and try again.');

                        // uninstall the system so we can repeat this step.
                        $this->migrations->reset();
                    }
                } catch (\Exception $e) {
                    $this->view->set('error', 'There was an error installing site specific settings. The installer will attempt to rollback the database, if this fails please delete all WHSuite related tables before trying installation again.');

                    // uninstall the system so we can repeat this step.
                    $this->migrations->reset();
                }

            } else {
                $this->view->set('error', 'There was a problem running the migrations. You may need to delete any tables that were created before the error.');
            }
        }

        list($folder,$page) = explode('/install', $_SERVER['REQUEST_URI'], 2);
        $siteUrl = 'http://' . $_SERVER['HTTP_HOST'] . $folder;

        $this->view->set('siteUrlPlaceholder', $siteUrl);

        return $this->view->display('configureSystem.php');
    }

    public function finish()
    {
        return $this->view->display('finish.php');
    }

    protected function alreadyInstalled($dbName, $dbPrefix)
    {
        $whsTables = array('addon_migrations', 'addons', 'announcements', 'automations', 'ban_lists', 'billing_periods', 'client_ach', 'client_cc', 'client_emails', 'client_group_links', 'client_groups', 'client_notes', 'client_throttles', 'clients', 'contact_extensions', 'contacts', 'countries', 'currencies', 'data_field_values', 'data_fields', 'data_groups', 'domain_extensions', 'domain_pricings', 'domains', 'email_template_translations', 'email_templates', 'gateway_currencies', 'gateway_log', 'gateways', 'group_permissions', 'hostings', 'invoice_items', 'invoices', 'kb_articles', 'kb_categories', 'language_phrases', 'languages', 'languages_installed', 'logs', 'menu_groups', 'menu_links', 'migrations', 'orders', 'permission_types', 'product_addon_pricings', 'product_addon_products', 'product_addon_purchases', 'product_addons', 'product_datas', 'product_groups', 'product_pricings', 'product_purchase_datas', 'product_purchases', 'product_types', 'products', 'promotion_billing_periods', 'promotion_discounts', 'promotion_products', 'promotions', 'registrars', 'server_groups', 'server_ips', 'server_modules', 'server_nameservers', 'servers', 'setting_categories', 'settings', 'shortcut_staff', 'shortcuts', 'staff_email_notifications', 'staff_group_support_department', 'staff_groups', 'staff_staff_group', 'staff_throttles', 'staff_widget', 'staffs', 'support_departments', 'support_posts', 'support_ticket_priorities', 'support_tickets', 'tax_levels', 'transactions', 'widgets');

        $showTables = Capsule::select("SHOW TABLES");

        $tables = array();
        foreach ($showTables as $table) {
            $tableName = $table['Tables_in_' . $dbName];

            $hasPrefix = (! empty($dbPrefix) && strpos($tableName, $dbPrefix) === 0);
            if ($hasPrefix || empty($dbPrefix)) {
                $tables[] = str_replace($dbPrefix, '', $tableName);
            }
        }

        // $tables = all tables that should apply for us to check
        $check = array_intersect($whsTables, $tables);

        if (! empty($check)) {
            return true;
        }

        return false;
    }

    protected function strictModeOn()
    {
        $globalStrictMode = Capsule::select('SELECT @@GLOBAL.sql_mode;');
        $sessionStrictMode = Capsule::select('SELECT @@SESSION.sql_mode;');

        $globalStrictMode = array_shift($globalStrictMode);
        $globalStrictMode = array_shift($globalStrictMode);

        $sessionStrictMode = array_shift($sessionStrictMode);
        $sessionStrictMode = array_shift($sessionStrictMode);

        if (stripos($globalStrictMode, 'strict') !== false || stripos($sessionStrictMode, 'strict') !== false) {
            return true;
        }

        return false;
    }
}
