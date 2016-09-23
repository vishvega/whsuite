<?php
namespace App\Storage\Migrations;

use \App\Libraries\BaseMigration;
use \App\Libraries\LanguageHelper;

class Migration2015_11_18_224500_version_1_0_0_structure extends BaseMigration
{
    public function up()
    {
         // --------------------------------------------------

        $this->createTable('addon_migrations', function ($table) {
            $table->string('migration', 255)->primary('migration');
            $table->string('addon', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('addons', function ($table) {
            $table->increments('id');
            $table->string('directory', 100)->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->tinyInteger('is_server')->nullable();
            $table->tinyInteger('is_gateway')->nullable();
            $table->tinyInteger('is_registrar')->nullable();
            $table->string('version', 15);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('announcements', function ($table) {
            $table->increments('id');
            $table->string('title', 100)->nullable();
            $table->text('body')->nullable();
            $table->tinyInteger('is_published')->nullable();
            $table->text('keywords')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('publish_date')->nullable();
            $table->integer('language_id')->nullable();
            $table->tinyInteger('individual_language_only')->nullable();
            $table->tinyInteger('clients_only')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('automations', function ($table) {
            $table->increments('id')->unsigned();
            $table->string('slug', 50);
            $table->dateTime('last_run')->default("0000-00-00 00:00:00");
            $table->integer('run_period');
            $table->timestamp('created_at')->default("0000-00-00 00:00:00");
            $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
        });


        // --------------------------------------------------

        $this->createTable('ban_lists', function ($table) {
            $table->increments('id');
            $table->string('ip_address', 46)->nullable();
            $table->string('email_address', 255)->nullable();
            $table->string('email_domain', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('billing_periods', function ($table) {
            $table->increments('id');
            $table->string('name', 30)->nullable();
            $table->integer('days')->nullable();
            $table->integer('sort')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('client_ach', function ($table) {
            $table->increments('id');
            $table->integer('client_id')->nullable();
            $table->string('first_name', 150)->nullable();
            $table->string('last_name', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('customer_type', 50)->nullable();
            $table->string('address1', 150)->nullable();
            $table->string('address2', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postcode', 50)->nullable();
            $table->string('country', 255)->nullable();
            $table->enum('account_type', array('savings', 'checking'))->nullable();
            $table->text('account_number')->nullable();
            $table->text('account_routing_number')->nullable();
            $table->text('account_last4')->nullable();
            $table->integer('gateway_id')->nullable();
            $table->text('gateway_data')->nullable();
            $table->integer('currency_id')->nullable();
            $table->tinyInteger('is_default')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('client_cc', function ($table) {
            $table->increments('id');
            $table->integer('client_id')->nullable();
            $table->string('first_name', 150)->nullable();
            $table->string('last_name', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('customer_type', 50)->nullable();
            $table->string('address1', 150)->nullable();
            $table->string('address2', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postcode', 50)->nullable();
            $table->string('country', 255)->nullable();
            $table->text('account_number')->nullable();
            $table->text('account_expiry')->nullable();
            $table->text('account_last4')->nullable();
            $table->string('account_type', 25)->nullable();
            $table->integer('gateway_id')->nullable();
            $table->text('gateway_data')->nullable();
            $table->integer('currency_id')->nullable();
            $table->tinyInteger('is_default')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('client_emails', function ($table) {
            $table->increments('id');
            $table->integer('client_id')->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('body')->nullable();
            $table->text('to')->nullable();
            $table->text('cc')->nullable();
            $table->text('bcc')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('client_group_links', function ($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('group_id');
        });


        // --------------------------------------------------

        $this->createTable('client_groups', function ($table) {
            $table->increments('id');
            $table->string('name', 255)->nullable();
            $table->text('permissions')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('client_notes', function ($table) {
            $table->increments('id');
            $table->integer('client_id')->nullable();
            $table->text('note')->nullable();
            $table->integer('staff_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('client_throttles', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('ip_address', 255)->nullable();
            $table->integer('attempts')->nullable();
            $table->tinyInteger('suspended')->nullable();
            $table->tinyInteger('banned')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('suspended_at')->default("0000-00-00 00:00:00");
            $table->timestamp('banned_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('clients', function ($table) {
            $table->increments('id');
            $table->text('hash');
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('company', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->tinyInteger('html_emails')->nullable();
            $table->string('address1', 150)->nullable();
            $table->string('address2', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('state', 150)->nullable();
            $table->string('postcode', 50)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('phone', 25)->nullable();
            $table->integer('currency_id');
            $table->tinyInteger('status')->nullable();
            $table->integer('language_id')->nullable()->default("1");
            $table->integer('is_taxexempt')->nullable();
            $table->string('first_ip', 46)->nullable();
            $table->string('first_hostname', 255)->nullable();
            $table->string('last_ip', 46)->nullable();
            $table->string('last_hostname', 255)->nullable();
            $table->timestamp('last_login')->nullable();
            $table->text('permissions')->nullable();
            $table->tinyInteger('activated')->nullable();
            $table->string('activation_code', 255)->nullable();
            $table->string('activated_at', 255)->nullable();
            $table->string('persist_code', 255)->nullable();
            $table->string('reset_password_code', 255)->nullable();
            $table->tinyInteger('guest_account')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('contact_extensions', function ($table) {
            $table->increments('id');
            $table->string('name', 45)->nullable();
            $table->integer('extension_id')->nullable();
            $table->integer('is_active')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('contacts', function ($table) {
            $table->increments('id');
            $table->integer('client_id')->nullable();
            $table->integer('contact_extension_id')->nullable();
            $table->enum('contact_type', array('registrant','administrative','technical','billing'))->nullable();
            $table->string('title', 50)->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('job_title', 150)->nullable();
            $table->string('address1', 150)->nullable();
            $table->string('address2', 150)->nullable();
            $table->string('address3', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('state', 150)->nullable();
            $table->string('postcode', 50)->nullable();
            $table->string('country', 150)->nullable();
            $table->integer('phone_cc')->nullable();
            $table->integer('phone')->nullable();
            $table->integer('fax_cc')->nullable();
            $table->integer('fax')->nullable();
            $table->text('custom_params')->nullable();
            $table->integer('registrar_id')->nullable();
            $table->text('registrar_data')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('countries', function ($table) {
            $table->increments('id');
            $table->string('iso_code', 2)->nullable();
            $table->string('name', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('currencies', function ($table) {
            $table->increments('id');
            $table->string('code', 3)->unique();
            $table->string('prefix', 10)->nullable();
            $table->string('suffix', 10)->nullable();
            $table->integer('decimals')->nullable();
            $table->string('decimal_point', 5)->nullable()->default(".");
            $table->string('thousand_separator', 5)->nullable()->default(",");
            $table->decimal('conversion_rate', 10, 2)->nullable();
            $table->tinyInteger('auto_update')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('data_field_values', function ($table) {
            $table->increments('id');
            $table->integer('data_field_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->text('value')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('data_fields', function ($table) {
            $table->increments('id');
            $table->string('slug', 100);
            $table->integer('data_group_id');
            $table->string('title', 100);
            $table->string('type', 30)->nullable();
            $table->string('help_text', 255)->nullable();
            $table->string('placeholder', 255)->nullable();
            $table->text('value_options')->nullable();
            $table->tinyInteger('is_editable')->nullable();
            $table->tinyInteger('is_staff_only')->nullable();
            $table->text('validation_rules')->nullable();
            $table->text('custom_regex')->nullable();
            $table->integer('sort')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('data_groups', function ($table) {
            $table->increments('id');
            $table->string('slug', 100)->unique();
            $table->string('name', 100)->nullable();
            $table->integer('addon_id')->nullable();
            $table->tinyInteger('is_editable')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('domain_extensions', function ($table) {
            $table->increments('id');
            $table->string('extension', 65)->nullable();
            $table->integer('registrar_id')->nullable();
            $table->integer('product_id');
            $table->integer('automatic_registration')->nullable();
            $table->integer('sort')->nullable();
            $table->tinyInteger('has_eppcode')->nullable();
            $table->integer('min_years')->default("1");
            $table->integer('max_years')->default("10");
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('domain_pricings', function ($table) {
            $table->increments('id');
            $table->string('domain_extension_id', 45)->nullable();
            $table->integer('years')->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('registration', 13, 4)->nullable();
            $table->decimal('renewal', 13, 4)->nullable();
            $table->decimal('transfer', 13, 4)->nullable();
            $table->decimal('restore', 13, 4)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('domains', function ($table) {
            $table->increments('id');
            $table->string('domain', 255)->nullable()->unique();
            $table->integer('product_purchase_id')->nullable();
            $table->integer('registrar_id')->nullable();
            $table->date('date_registered')->nullable();
            $table->date('date_expires')->nullable();
            $table->tinyInteger('registration_period')->nullable();
            $table->tinyInteger('renewal_disabled')->nullable();
            $table->text('nameservers')->nullable();
            $table->tinyInteger('registrar_lock')->nullable();
            $table->tinyInteger('enable_sync')->nullable();
            $table->text('registrar_data')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('email_template_translations', function ($table) {
            $table->increments('id');
            $table->integer('email_template_id')->nullable();
            $table->integer('language_id')->nullable();
            $table->string('subject', 100)->nullable();
            $table->text('html_body')->nullable();
            $table->text('html_body_default')->nullable();
            $table->text('plaintext_body')->nullable();
            $table->text('plaintext_body_default')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('email_templates', function ($table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->string('slug', 50)->nullable();
            $table->text('cc')->nullable();
            $table->text('bcc')->nullable();
            $table->text('available_tags')->nullable();
            $table->integer('addon_id');
            $table->tinyInteger('is_system')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('gateway_currencies', function ($table) {
            $table->increments('id');
            $table->integer('gateway_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('sort')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('gateway_log', function ($table) {
            $table->increments('id');
            $table->integer('gateway_id')->nullable();
            $table->text('data')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('gateways', function ($table) {
            $table->increments('id');
            $table->string('slug', 50)->nullable();
            $table->string('name', 50)->nullable();
            $table->integer('addon_id')->nullable();
            $table->tinyInteger('is_merchant');
            $table->tinyInteger('process_cc')->nullable();
            $table->tinyInteger('store_cc')->nullable();
            $table->tinyInteger('process_ach')->nullable();
            $table->tinyInteger('store_ach')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->integer('sort')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('group_permissions', function ($table) {
            $table->increments('id');
            $table->integer('staff_group_id')->nullable();
            $table->integer('permission_type_id')->nullable();
            $table->tinyInteger('permision')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('hostings', function ($table) {
            $table->increments('id');
            $table->integer('product_purchase_id')->nullable();
            $table->integer('server_id')->nullable();
            $table->text('domain')->nullable();
            $table->text('nameservers')->nullable();
            $table->integer('diskspace_limit')->nullable();
            $table->integer('diskspace_usage')->nullable();
            $table->integer('bandwidth_limit')->nullable();
            $table->integer('bandwidth_usage')->nullable();
            $table->integer('last_sync')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->text('username')->nullable();
            $table->text('password')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('invoice_items', function ($table) {
            $table->increments('id');
            $table->integer('invoice_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('product_purchase_id')->nullable();
            $table->integer('product_addon_purchase_id')->nullable();
            $table->string('service_type', 45)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_taxed')->nullable()->default("1");
            $table->decimal('total', 13, 4)->nullable();
            $table->date('date_due')->nullable();
            $table->decimal('promotion_discount', 13, 4)->nullable();
            $table->tinyInteger('promotion_is_percentage')->nullable();
            $table->tinyInteger('promotion_before_tax')->nullable();
            $table->tinyInteger('is_account_credit');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('invoices', function ($table) {
            $table->increments('id');
            $table->integer('invoice_no')->nullable()->unique();
            $table->integer('client_id')->nullable();
            $table->integer('currency_id');
            $table->date('date_due')->nullable();
            $table->dateTime('date_paid')->nullable();
            $table->decimal('subtotal', 13, 4)->nullable();
            $table->decimal('level1_rate', 10, 2)->nullable();
            $table->decimal('level1_total', 13, 4)->nullable();
            $table->decimal('level2_rate', 10, 2)->nullable();
            $table->decimal('level2_total', 13, 4)->nullable();
            $table->decimal('pre_tax_discount', 13, 4)->nullable();
            $table->decimal('post_tax_discount', 13, 4)->nullable();
            $table->decimal('total', 13, 4)->nullable();
            $table->decimal('total_paid', 13, 4)->nullable();
            $table->tinyInteger('status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('language_phrases', function ($table) {
            $table->increments('id');
            $table->integer('language_id')->nullable();
            $table->string('slug', 50)->nullable();
            $table->text('text')->nullable();
            $table->integer('addon_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('languages', function ($table) {
            $table->increments('id');
            $table->string('name', 150)->nullable();
            $table->string('slug', 150)->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->tinyInteger('is_default');
            $table->string('language_code', 30)->nullable();
            $table->enum('text_direction', array('LTR','RTL'))->nullable()->default("LTR");
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('languages_installed', function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('language_id');
            $table->integer('addon_id');
            $table->timestamp('created_at')->default("0000-00-00 00:00:00");
            $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
        });


        // --------------------------------------------------

        $this->createTable('logs', function ($table) {
            $table->increments('id');
            $table->integer('staff_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('action_type', 255)->nullable();
            $table->text('action')->nullable();
            $table->string('ip_address', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('menu_groups', function ($table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('menu_links', function ($table) {
            $table->increments('id');
            $table->integer('menu_group_id')->nullable();
            $table->string('title', 100)->nullable();
            $table->integer('parent_id')->nullable();
            $table->tinyInteger('is_link')->nullable();
            $table->string('url', 255)->nullable();
            $table->integer('sort')->nullable();
            $table->tinyInteger('clients_only')->nullable();
            $table->string('class', 255)->nullable();
            $table->integer('addon_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('migrations', function ($table) {
            $table->string('migration', 255)->primary('migration');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('orders', function ($table) {
            $table->increments('id');
            $table->integer('client_id')->nullable();
            $table->integer('order_no')->nullable();
            $table->integer('promotion_id')->nullable();
            $table->integer('promotion_type')->nullable();
            $table->decimal('promotion_value', 13, 4)->nullable();
            $table->integer('gateway_id')->nullable();
            $table->string('user_ip', 46)->nullable();
            $table->string('user_hostname', 255)->nullable();
            $table->integer('status')->nullable();
            $table->text('fraud_output')->nullable();
            $table->text('notes')->nullable();
            $table->integer('activated_by')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('permission_types', function ($table) {
            $table->increments('id');
            $table->string('slug', 50)->nullable();
            $table->string('title', 100)->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('addon_id')->nullable();
            $table->integer('sort');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('product_addon_pricings', function ($table) {
            $table->increments('id');
            $table->integer('addon_id')->nullable();
            $table->integer('billing_period_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->string('price', 45)->nullable();
            $table->text('addon_value')->nullable();
            $table->tinyInteger('allow_in_signup')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('product_addon_products', function ($table) {
            $table->increments('id');
            $table->integer('product_addon_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('product_addon_purchases', function ($table) {
            $table->increments('id');
            $table->integer('product_purchase_id')->nullable();
            $table->integer('addon_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('first_payment', 10, 2)->nullable()->default("0.00");
            $table->decimal('recurring_payment', 10, 2)->nullable()->default("0.00");
            $table->tinyInteger('is_active')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('product_addons', function ($table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->string('addon_slug', 45)->nullable();
            $table->text('description')->nullable();
            $table->text('addon_value');
            $table->tinyInteger('is_free');
            $table->integer('sort')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('product_datas', function ($table) {
            $table->increments('id');
            $table->integer('product_id')->nullable();
            $table->integer('addon_id')->nullable();
            $table->string('slug', 100)->nullable();
            $table->text('value')->nullable();
            $table->tinyInteger('is_encrypted')->nullable();
            $table->tinyInteger('is_array')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('product_groups', function ($table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->string('slug', 100);
            $table->text('description')->nullable();
            $table->tinyInteger('is_visible')->nullable();
            $table->integer('sort')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('product_pricings', function ($table) {
            $table->increments('id');
            $table->integer('product_id')->nullable();
            $table->integer('currency_id');
            $table->integer('billing_period_id')->nullable();
            $table->decimal('price', 13, 4)->nullable()->default("0.0000");
            $table->decimal('renewal_price', 13, 4);
            $table->decimal('bandwidth_overage_fee', 13, 4)->nullable();
            $table->decimal('diskspace_overage_fee', 13, 4)->nullable();
            $table->decimal('setup', 13, 4)->nullable()->default("0.0000");
            $table->tinyInteger('allow_in_signup')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('product_purchase_datas', function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('product_purchase_id');
            $table->string('slug', 50);
            $table->text('value');
            $table->timestamp('created_at')->default("0000-00-00 00:00:00");
            $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
        });


        // --------------------------------------------------

        $this->createTable('product_purchases', function ($table) {
            $table->increments('id');
            $table->integer('client_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('currency_id');
            $table->integer('product_id')->nullable();
            $table->integer('billing_period_id')->nullable();
            $table->decimal('first_payment', 14, 3)->nullable()->default("0.000");
            $table->decimal('recurring_payment', 14, 3)->nullable();
            $table->date('next_renewal')->nullable();
            $table->date('next_invoice')->nullable();
            $table->integer('promotion_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('disable_autosuspend')->nullable();
            $table->text('suspend_notice')->nullable();
            $table->text('payment_subscription')->nullable();
            $table->integer('gateway_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('product_types', function ($table) {
            $table->increments('id');
            $table->string('name', 60)->nullable();
            $table->string('slug', 60)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_hosting')->nullable();
            $table->tinyInteger('is_domain')->nullable();
            $table->integer('addon_id');
            $table->integer('sort')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('products', function ($table) {
            $table->increments('id');
            $table->integer('product_type_id')->nullable();
            $table->integer('product_group_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('setup_automatically')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->tinyInteger('is_visible')->nullable();
            $table->tinyInteger('domain_type')->nullable();
            $table->integer('email_template_id')->nullable();
            $table->integer('stock')->nullable()->default("-1");
            $table->text('subdomains')->nullable();
            $table->integer('server_group_id')->nullable();
            $table->integer('auto_suspend_days')->nullable();
            $table->integer('suspend_email_template_id')->nullable();
            $table->integer('auto_terminate_days')->nullable();
            $table->integer('terminate_email_template_id')->nullable();
            $table->tinyInteger('charge_disk_overages')->nullable();
            $table->tinyInteger('charge_bandwidth_overages')->nullable();
            $table->tinyInteger('allow_ips')->nullable();
            $table->integer('included_ips')->nullable();
            $table->tinyInteger('allow_upgrade')->nullable();
            $table->text('upgrade_package_ids')->nullable();
            $table->integer('upgrade_email_template_id')->nullable();
            $table->tinyInteger('is_taxed')->nullable();
            $table->tinyInteger('is_free');
            $table->tinyInteger('affiliate_is_enabled')->nullable();
            $table->tinyInteger('affiliate_is_recurring')->nullable();
            $table->decimal('affiliate_amount', 10, 2)->nullable();
            $table->text('bundled_product_ids')->nullable();
            $table->integer('sort')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('promotion_billing_periods', function ($table) {
            $table->increments('id');
            $table->integer('promotion_id')->nullable();
            $table->integer('billing_period_id')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('promotion_discounts', function ($table) {
            $table->increments('id');
            $table->integer('promotion_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->tinyInteger('is_percentage')->nullable();
            $table->decimal('discount', 13, 4)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('promotion_products', function ($table) {
            $table->increments('id');
            $table->integer('promotion_id')->nullable();
            $table->integer('product_id')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('promotions', function ($table) {
            $table->increments('id');
            $table->string('code', 50)->unique();
            $table->tinyInteger('is_recurring')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->tinyInteger('max_uses')->nullable();
            $table->tinyInteger('total_uses')->nullable();
            $table->tinyInteger('once_per_order')->nullable();
            $table->tinyInteger('before_tax')->nullable();
            $table->integer('client_id')->nullable();
            $table->tinyInteger('require_group_purchase')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('registrars', function ($table) {
            $table->increments('id');
            $table->string('name', 50)->nullable();
            $table->string('slug', 50)->nullable();
            $table->integer('addon_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('server_groups', function ($table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('autofill')->nullable();
            $table->integer('default_server_id')->nullable();
            $table->integer('server_module_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('server_ips', function ($table) {
            $table->increments('id');
            $table->integer('server_id')->nullable();
            $table->string('ip_address', 46)->nullable();
            $table->integer('product_purchase_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('server_modules', function ($table) {
            $table->increments('id');
            $table->string('name', 50)->nullable();
            $table->string('slug', 50)->nullable();
            $table->integer('addon_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('server_nameservers', function ($table) {
            $table->increments('id');
            $table->integer('server_id')->nullable();
            $table->string('hostname', 255)->nullable();
            $table->text('ip_address')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('servers', function ($table) {
            $table->increments('id');
            $table->integer('server_group_id')->nullable();
            $table->string('name', 255)->nullable();
            $table->string('hostname', 255)->nullable();
            $table->string('main_ip', 46)->nullable();
            $table->string('location', 255)->nullable();
            $table->text('username')->nullable();
            $table->text('password')->nullable();
            $table->text('api_key')->nullable();
            $table->tinyInteger('ssl_connection')->nullable();
            $table->integer('max_accounts')->nullable();
            $table->text('nameservers')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->text('notes')->nullable();
            $table->string('status_url', 255)->nullable();
            $table->integer('priority');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('setting_categories', function ($table) {
            $table->increments('id');
            $table->string('slug', 50)->nullable();
            $table->string('title', 50)->nullable();
            $table->tinyInteger('is_visible');
            $table->integer('sort')->nullable();
            $table->integer('addon_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('settings', function ($table) {
            $table->increments('id');
            $table->string('slug', 100);
            $table->string('title', 60)->nullable();
            $table->string('description', 255)->nullable();
            $table->enum('field_type', array('text','textarea','wysiwyg','radio','select','checkbox','password'))->nullable();
            $table->string('rules', 255)->nullable();
            $table->text('options')->nullable();
            $table->string('placeholder', 255)->nullable();
            $table->integer('setting_category_id');
            $table->tinyInteger('editable')->nullable();
            $table->tinyInteger('required')->nullable();
            $table->integer('addon_id')->nullable();
            $table->integer('sort')->nullable();
            $table->text('value')->nullable();
            $table->text('default_value')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('shortcut_staff', function ($table) {
            $table->increments('id');
            $table->integer('shortcut_id');
            $table->integer('staff_id');
            $table->integer('sort')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('shortcuts', function ($table) {
            $table->increments('id');
            $table->string('unique_name', 100);
            $table->integer('addon_id')->nullable();
            $table->string('name', 250);
            $table->string('icon_class', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('route', 250);
            $table->string('label_route', 250)->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('staff_email_notifications', function ($table) {
            $table->increments('id');
            $table->integer('staff_id');
            $table->integer('email_template_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('staff_groups', function ($table) {
            $table->increments('id');
            $table->string('name', 255)->nullable();
            $table->text('permissions')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('staff_staff_group', function ($table) {
            $table->increments('id');
            $table->integer('staff_id');
            $table->integer('staff_group_id');
        });


        // --------------------------------------------------

        $this->createTable('staff_throttles', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('ip_address', 255)->nullable();
            $table->integer('attempts')->nullable();
            $table->tinyInteger('suspended')->nullable();
            $table->tinyInteger('banned')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('staff_widget', function ($table) {
            $table->increments('id');
            $table->integer('widget_id');
            $table->integer('staff_id');
            $table->integer('sort')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('staffs', function ($table) {
            $table->increments('id');
            $table->string('email', 255)->nullable();
            $table->text('password')->nullable();
            $table->text('permissions')->nullable();
            $table->tinyInteger('activated')->nullable();
            $table->string('activation_code', 255)->nullable();
            $table->string('activated_at', 255)->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('persist_code', 255)->nullable();
            $table->string('reset_password_code', 255)->nullable();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->integer('language_id')->nullable()->default("1");
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('tax_levels', function ($table) {
            $table->increments('id');
            $table->string('name', 45)->nullable();
            $table->tinyInteger('level')->nullable()->default("1");
            $table->decimal('rate', 10, 2)->nullable();
            $table->string('state', 255)->nullable();
            $table->string('country', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('transactions', function ($table) {
            $table->increments('id');
            $table->integer('client_id')->nullable();
            $table->integer('gateway_id')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->integer('currency_id')->nullable();
            $table->string('description', 255)->nullable();
            $table->text('gateway_token')->nullable();
            $table->text('data')->nullable();
            $table->enum('type', array('receipt','invoice','debit','credit_usage','void','refunded'))->nullable();
            $table->decimal('amount', 13, 4)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        // --------------------------------------------------

        $this->createTable('widgets', function ($table) {
            $table->increments('id');
            $table->string('unique_name', 100)->unique();
            $table->integer('addon_id')->nullable();
            $table->string('name', 250);
            $table->text('description')->nullable();
            $table->string('route', 250);
            $table->tinyInteger('is_active')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        $this->dropTable('addon_migrations');
        $this->dropTable('addons');
        $this->dropTable('announcements');
        $this->dropTable('automations');
        $this->dropTable('ban_lists');
        $this->dropTable('billing_periods');
        $this->dropTable('client_ach');
        $this->dropTable('client_cc');
        $this->dropTable('client_emails');
        $this->dropTable('client_group_links');
        $this->dropTable('client_groups');
        $this->dropTable('client_notes');
        $this->dropTable('client_throttles');
        $this->dropTable('clients');
        $this->dropTable('contact_extensions');
        $this->dropTable('contacts');
        $this->dropTable('countries');
        $this->dropTable('currencies');
        $this->dropTable('data_field_values');
        $this->dropTable('data_fields');
        $this->dropTable('data_groups');
        $this->dropTable('domain_extensions');
        $this->dropTable('domain_pricings');
        $this->dropTable('domains');
        $this->dropTable('email_template_translations');
        $this->dropTable('email_templates');
        $this->dropTable('gateway_currencies');
        $this->dropTable('gateway_log');
        $this->dropTable('gateways');
        $this->dropTable('group_permissions');
        $this->dropTable('hostings');
        $this->dropTable('invoice_items');
        $this->dropTable('invoices');
        $this->dropTable('language_phrases');
        $this->dropTable('languages');
        $this->dropTable('languages_installed');
        $this->dropTable('logs');
        $this->dropTable('menu_groups');
        $this->dropTable('menu_links');
        $this->dropTable('migrations');
        $this->dropTable('orders');
        $this->dropTable('permission_types');
        $this->dropTable('product_addon_pricings');
        $this->dropTable('product_addon_products');
        $this->dropTable('product_addon_purchases');
        $this->dropTable('product_addons');
        $this->dropTable('product_datas');
        $this->dropTable('product_groups');
        $this->dropTable('product_pricings');
        $this->dropTable('product_purchase_datas');
        $this->dropTable('product_purchases');
        $this->dropTable('product_types');
        $this->dropTable('products');
        $this->dropTable('promotion_billing_periods');
        $this->dropTable('promotion_discounts');
        $this->dropTable('promotion_products');
        $this->dropTable('promotions');
        $this->dropTable('registrars');
        $this->dropTable('server_groups');
        $this->dropTable('server_ips');
        $this->dropTable('server_modules');
        $this->dropTable('server_nameservers');
        $this->dropTable('servers');
        $this->dropTable('setting_categories');
        $this->dropTable('settings');
        $this->dropTable('shortcut_staff');
        $this->dropTable('shortcuts');
        $this->dropTable('staff_email_notifications');
        $this->dropTable('staff_groups');
        $this->dropTable('staff_staff_group');
        $this->dropTable('staff_throttles');
        $this->dropTable('staff_widget');
        $this->dropTable('staffs');
        $this->dropTable('tax_levels');
        $this->dropTable('transactions');
        $this->dropTable('widgets');
    }
}
