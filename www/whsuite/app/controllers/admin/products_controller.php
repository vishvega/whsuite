<?php
/**
 * Products Admin Controller
 *
 * The products admin controller handles all CRUD operations for products and
 * product groups.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
use \Illuminate\Support\Str;

class ProductsController extends AdminController
{
    /**
     * List Products
     */
    public function listProducts()
    {
        $title = $this->lang->get('product_management');

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('product_groups', ProductGroup::all());

        $toolbar = array(
            array(
                'url_route'=> 'admin-productgroup-add',
                'icon' => 'fa fa-plus',
                'label' => 'new_product_group'
            ),
        );
        $this->view->set('toolbar', $toolbar);

        $this->view->display('products/listProducts.php');
    }
    /**
     * New Group
     *
     * Add a new product group
     */
    public function newGroup()
    {
        $title = $this->lang->get('new_product_group');

        if (\Whsuite\Inputs\Post::get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Group'), ProductGroup::$rules);
            $group = new ProductGroup();

            if ($validator->fails()) {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            } elseif (!$group->validateCustomFields(false)) {
                // For now we set this as a flash error. In a future update we'll prevent the need to redirect/reload.
                App::get('session')->setFlash('error', $this->lang->get('error_adding_product_group'));
                return $this->redirect('admin-productgroup-add');
            } else {
                $group_data = \Whsuite\Inputs\Post::get('Group');

                $group->name = $group_data['name'];
                $group->description = $group_data['description'];
                $group->is_visible = $group_data['is_visible'];
                $group->sort = $group_data['sort'];

                if ($group->save() && $group->saveCustomFields(false)) {
                    App::get('session')->setFlash('success', $this->lang->get('product_group_added'));
                    return $this->redirect('admin-productgroup-manage', ['id' => $group->id]);
                } else {
                    \App\Libraries\Message::set($this->lang->get('error_adding_product_group'), 'fail');
                }

                \Whsuite\Inputs\Post::set('Group', $group->toArray());
            }
        }

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('product_management'), 'admin-product');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('group', new ProductGroup());
        $this->view->display('products/newGroup.php');
    }

    /**
     * Manage Group
     *
     * Manage a product group.
     *
     * @param int $id ID of the group to manage
     */
    public function manageGroup($id)
    {
        $group = ProductGroup::find($id);
        if (empty($group)) {
            return $this->redirect('admin-product');
        }

        $title = $this->lang->get('product_group').' - '.$group->name;

        if (\Whsuite\Inputs\Post::get()) {
            $validator = $this->validator->make(\Whsuite\Inputs\Post::get('Group'), ProductGroup::$rules);

            if (!$validator->fails()) {
                if ($group->validateCustomFields(false)) {
                    $group_data = \Whsuite\Inputs\Post::get('Group');

                    $group->name = $group_data['name'];
                    $group->description = $group_data['description'];
                    $group->is_visible = $group_data['is_visible'];
                    $group->sort = $group_data['sort'];

                    if ($group->save() && $group->saveCustomFields(false)) {
                        App::get('session')->setFlash('success', $this->lang->get('product_group_updated'));
                        return $this->redirect('admin-productgroup-manage', ['id' => $group->id]);
                    } else {
                        \App\Libraries\Message::set($this->lang->get('error_saving_product_group'), 'fail');
                    }
                } else {
                    // For now we set this as a flash error. In a future update we'll prevent the need to redirect/reload.
                    App::get('session')->setFlash('error', $this->lang->get('error_updating_product_group'));
                    return $this->redirect('admin-productgroup-manage', ['id' => $group->id]);
                }
            } else {
                \App\Libraries\Message::set($this->lang->formatErrors($validator->messages()), 'fail');
            }
        }

        \Whsuite\Inputs\Post::set('Group', $group->toArray());

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('product_management'), 'admin-product');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('group', $group);
        $this->view->set('products', $group->Product()->orderByRaw('sort = 0, sort ASC')->get());

        $toolbar = array(
            array(
                'url_route'=> 'admin-product-add',
                'route_params' => array('id' => $group->id),
                'icon' => 'fa fa-plus',
                'label' => 'new_product'
            ),
        );
        $this->view->set('toolbar', $toolbar);

        $this->view->display('products/manageGroup.php');
    }

    /**
     * Delete Group
     *
     * Delete a product group. A group can only be deleted if its empty.
     *
     * @param int $id ID of the group to delete
     */
    public function deleteGroup($id)
    {
        $group = ProductGroup::find($id);
        $product_count = $group->Product()->count();
        if (empty($group) || $product_count > 0) {
            // The group either does not exist, or it has products still assigned
            // to it, in which case it's not allowed to be deleted. Redirect back
            // to the product group listing page.

            return $this->redirect('admin-product');
        }

        if ($group->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('product_group_successfully_deleted'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_deleting_product_group'));
        }

        return $this->redirect('admin-product');
    }

    /**
     * New Product
     *
     * Create a new product.
     *
     * @param id Product Group ID to add the product to
     */
    public function newProduct($id)
    {
        $group = ProductGroup::find($id);
        if (empty($group)) {
            return $this->redirect('admin-product');
        }

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('product_management'), 'admin-product');

        $this->view->set('email_templates', EmailTemplate::formattedList('id', 'name'));

        $post_data = \Whsuite\Inputs\Post::get();
        if (isset($post_data['create_product'])) {
            // This is the final stage. At this point we're going to create the
            // product assuming all the data was entered correctly.

            // Validation here is a bit complex, and we need somewhere to store
            // the validation messages so we can return them all at once.
            $validation_data = array(
                'result' => 'success',
                'errors' => array()
            );

            // Merge the two arrays
            $product_data = $post_data['Product'];
            if (isset($post_data['PackageMeta'])) {
                $meta_data = $post_data['PackageMeta'];
            } else {
                $meta_data = array();
            }

            $pricing_data = null;

            if (isset($post_data['Pricing'])) {
                $pricing_data = $post_data['Pricing'];
            }

            $product_type = ProductType::find($product_data['product_type_id']);
            if (empty($product_type)) {
                return $this->redirect('admin-product');
            }

            // Work out which addon we're using here.
            $addon_id = 0;

            if (isset($product_data['server_group']) && $product_data['server_group'] > 0) {
                $server_group = ServerGroup::find($product_data['server_group']);

                if (!empty($server_group) && $server_group->server_module_id > 0) {
                    $server_module = $server_group->ServerModule()->first();

                    if ($server_module->addon_id > 0) {
                        $addon = $server_module->Addon()->first();
                        $addon_id = null;

                        if (is_object($addon)) {
                            $addon_id = $addon->id;
                        }
                    }
                }
            }

            // Set the post data again, so that if needed it can be re-used. This
            // is mainly if the data fails validation.
            \Whsuite\Inputs\Post::set('Product', $product_data);
            \Whsuite\Inputs\Post::set('PackageMeta', $meta_data);
            \Whsuite\Inputs\Post::set('Pricing', $pricing_data);

            // Validate the product data
            $validator = $this->validator->make($product_data, Product::$rules);

            if ($validator->fails()) {
                $validation_data['result'] = 'error';
                $validation_data['errors'] = $validation_data['errors'] + $validator->messages()->toArray();
            }

            $product = new Product();
            $product->product_type_id = $product_data['product_type_id'];
            $product->product_group_id = $group->id;
            $product->name = $product_data['name'];
            $product->description = $product_data['description'];
            $product->setup_automatically = 1;
            $product->is_active = $product_data['is_active'];
            $product->is_visible = $product_data['is_visible'];
            $product->email_template_id = $product_data['email_template_id'];
            $product->stock = $product_data['stock'];
            $product->auto_suspend_days = $product_data['auto_suspend_days'];
            $product->suspend_email_template_id = $product_data['suspend_email_template_id'];
            $product->auto_terminate_days = $product_data['auto_terminate_days'];
            $product->terminate_email_template_id = $product_data['terminate_email_template_id'];
            $product->is_taxed = $product_data['is_taxed'];
            $product->is_free = 0;
            $product->sort = $product_data['sort'];

            if ($product_type->is_hosting == '1') {
                // Because we cant validate pricing data very easily, we're going to
                // basically loop through each pricing record, and run it against the
                // validation, compiling a bunch of validation messages should we find
                // any issues.

                foreach ($pricing_data as $billing_period_id => $data) {
                    foreach ($data as $currency_id => $price_data) {
                        if (! empty($price_data['price'])) {
                            $compiled_pricing_data = array(
                                'product_id' => '0',
                                'currency_id' => $currency_id,
                                'billing_period_id' => $billing_period_id,
                                'price' => $price_data['price'],
                                'renewal_price' => $price_data['renewal_price'],
                                'setup' => $price_data['setup'],
                                'bandwidth_overage_fee' => $price_data['bandwidth_overage_fee'],
                                'diskspace_overage_fee' => $price_data['diskspace_overage_fee'],
                                'allow_in_signup' => $price_data['allow_in_signup']
                            );
                            $pricing_validator = $this->validator->make($compiled_pricing_data, ProductPricing::$rules);

                            if ($pricing_validator->fails()) {
                                $validation_data['result'] = 'error';
                                $validation_data['errors'] = $validation_data['errors'] + $pricing_validator->messages();
                            }
                        }
                    }
                }

                // Now we've got to not only check the product type, but validate it.
                // This cant realistically be done here, so we've got two seperate
                // methods for both hosting and domains. One does validation, the other
                // does any inserts.

                $product->domain_type = $product_data['domain_type'];
                $product->server_group_id = $product_data['server_group_id'];
                $product->charge_disk_overages = $product_data['charge_disk_overages'];
                $product->charge_bandwidth_overages = $product_data['charge_bandwidth_overages'];
                $product->allow_ips = $product_data['allow_ips'];
                $product->included_ips = $product_data['included_ips'];
                $product->allow_upgrade = 0;
                $product->upgrade_package_ids = '';
                $product->upgrade_email_template_id = 0;

                $hosting_validation = $this->hostingValidation($product_data);

                if ($hosting_validation['result'] == 'error') {
                    $validation_data['result'] = 'error';
                    $validation_data['errors'] = $validation_data['errors'] + $hosting_validation['errors'];
                }

            } elseif ($product_type->is_domain == '1') {
                if (! isset($post_data['Domain'])) {
                    $validation_data['errors'][] = 'No Domain Extension Provided.';
                    $domain_data = null;
                } else {
                    $domain_data = $post_data['Domain'];
                }

                \Whsuite\Inputs\Post::set('Domain', $domain_data);

                $domain_validation = $this->domainValidation($product_data, $domain_data);

                if ($domain_validation['result'] == 'error') {
                    $validation_data['result'] = 'error';
                    $validation_data['errors'] = $validation_data['errors'] + $domain_validation['errors'];
                }

                $domain_extension = DomainExtension::find($domain_data['extension']);
                if (empty($domain_extension)) {
                    return $this->redirect('admin-product');
                }
            } elseif ($product_type->addon_id > 0) {
                // Product type is managed by an addon - we'll allow the addon
                // to have a copy of all processed data here so it can perform
                // any actions it needs to.
                $addon = \Addon::find($product_type->addon_id);

                $addon_details = $addon->details();

                $addon_cameled = Str::studly($addon->directory);

                // Load the addon product types handler
                $product_type_helper = \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\ProductTypesHelper');

                $validate_addon_tabs  = $product_type_helper->validateAddonProductData($product_type, $post_data);

                if (is_array($validate_addon_tabs) && ! empty($validate_addon_tabs)) {
                    $validation_data = $validate_addon_tabs;
                }
            }

            if ($validation_data['result'] == 'success') {
                // Insert product
                $product_status = $product->save();

                // As default the pricing status is true as it's possible to not
                // have any price records at all if the product is free.
                $pricing_status = true;

                // If applicable, update domain extension
                if (isset($domain_extension)) {
                    $domain_extension->product_id = $product->id;
                    $domain_extension->save();
                }

                if ($product_type->is_domain == '1') {
                    foreach ($pricing_data as $billing_period => $data) {
                        foreach ($data as $currency_id => $price_data) {
                            if (! empty($price_data['registration'])) {
                                $domain_pricing = new DomainPricing();
                                $domain_pricing->domain_extension_id = $domain_extension->id;
                                $domain_pricing->years = $billing_period;
                                $domain_pricing->currency_id = $currency_id;
                                $domain_pricing->registration = $price_data['registration'];
                                $domain_pricing->renewal = $price_data['renewal'];
                                $domain_pricing->transfer = $price_data['transfer'];
                                $domain_pricing->restore = $price_data['restore'];

                                $pricing_status = $domain_pricing->save();
                            }
                        }
                    }
                } elseif ($product_type->addon_id > 0) {
                    $addon_details = $addon->details();

                    $addon_cameled = Str::studly($addon->directory);

                    // Load the addon product types handler
                    $product_type_helper = \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\ProductTypesHelper');

                    $save_addon_data  = $product_type_helper->saveAddonProductData($product_type, $post_data, $product);

                } else {
                    // Insert product pricing
                    foreach ($pricing_data as $billing_period_id => $data) {
                        foreach ($data as $currency_id => $price_data) {
                            if (! empty($price_data['price'])) {
                                if (isset($pricing_status) && $pricing_status == false) {
                                    // An error occurred at some point when adding
                                    // one of the pricing data records, skip over
                                    // the rest so we can show an unexpected error.
                                    continue;
                                }

                                $pricing_record = new ProductPricing();
                                $pricing_record->product_id = $product->id;
                                $pricing_record->currency_id = $currency_id;
                                $pricing_record->billing_period_id = $billing_period_id;
                                $pricing_record->price = $price_data['price'];
                                $pricing_record->renewal_price = $price_data['renewal_price'];
                                $pricing_record->setup = $price_data['setup'];
                                $pricing_record->bandwidth_overage_fee = $price_data['bandwidth_overage_fee'];
                                $pricing_record->diskspace_overage_fee = $price_data['diskspace_overage_fee'];
                                $pricing_record->allow_in_signup = $price_data['allow_in_signup'];

                                $pricing_status = $pricing_record->save();
                            }
                        }
                    }
                }

                // If applicable, add product meta data
                foreach ($meta_data as $slug => $value) {
                    $metadata = new ProductData();
                    $metadata->product_id = $product->id;
                    $metadata->addon_id = $addon_id;
                    $metadata->slug = $slug;
                    $metadata->value = $value;
                    $metadata->is_encrypted = 0;
                    $metadata->is_array = 0;

                    $metadata->save();
                }

                if ($product_status && $pricing_status) {
                    // All good!
                    App::get('session')->setFlash('success', $this->lang->get('product_added'));
                    return $this->redirect('admin-product-manage', ['id' => $group->id, 'product_id' => $product->id]);
                } else {
                    // Something went wrong with the insert, but we dont really know what.
                    \Whsuite\Inputs\Post::set('submit_step1', '1');
                    \App\Libraries\Message::set($this->lang->get('an_error_occurred'), 'fail');
                }
            } else {
                \Whsuite\Inputs\Post::set('submit_step1', '1');
                \App\Libraries\Message::set(
                    $this->lang->formatErrors(
                        json_encode($validation_data['errors'])
                    ),
                    'fail'
                );
            }
        }

        $post_data = \Whsuite\Inputs\Post::get();

        if (isset($post_data['submit_step1'])) {
            $product_type = ProductType::find($post_data['Product']['product_type_id']);
            if (empty($product_type)) {
                return $this->redirect('admin-product-add', ['id' => $group->id]);
            }

            // Step one is completed, we now need to show the step2 form.
            // The step2 form can contain either a domain-specific or hosting-specific
            // set of fields, along with any pricing options.
            //
            // We make use of ajax to load in some of the inputs as we need to call
            // out to any server/domain addons for retrieving things like package
            // names. Generally these will be calling out to a remote server to get
            // package names or any other server info. This will be stored in
            // package meta later.

            // NOTE: This is a temporary step until our form system is slightly more refined. The
            // stock level field needs to be set to -1 to disable it. Leaving it blank will
            // give the same effect as zero (i.e no stock). So on new products, we pre-fill it
            // with -1 to overcome this. This will be improved upon in a future update.
            \Whsuite\Inputs\Post::set('Product.stock', '-1');

            $title = $this->lang->get('new_product_step_two');
            App::get('breadcrumbs')->add($title);
            App::get('breadcrumbs')->build();

            $this->view->set('title', $title);
            $this->view->set('group', $group);

            $this->view->set('server_groups', ServerGroup::formattedList('id', 'name', array(), 'name', 'desc', true));

            $domain_types = array();
            foreach (Product::$product_domain_options as $id => $type) {
                $domain_types[$id] = $this->lang->get($type);
            }
            $this->view->set('domain_types', $domain_types);

            $product_setup_options = array();

            foreach (Product::$product_setup_options as $id => $value) {
                $product_setup_options[$id] = $this->lang->get($value);
            }

            $this->view->set('product_setup_options', $product_setup_options);

            // Load the product type so we can determin which (if any) extra forms
            // to load in.

            $type = ProductType::find($post_data['Product']['product_type_id']);

            if ($type && $type->is_domain == '1') {
                // We need to run a check on domains. If someone tries creating a domain
                // product when no domains extensions exist, or when all extensions are
                // already in use, we want to kick them back to the product listing page
                // with an error message.
                $extensions = DomainExtension::formattedList('id', 'extension', array(
                    array(
                        'column' => 'product_id',
                        'operator' => '=',
                        'value' => '0',
                        'type' => 'and'
                    )
                ));

                if (! $extensions) {
                    App::get('session')->setFlash('error', $this->lang->get('no_domain_extensions_exist'));
                    return $this->redirect('admin-product');
                }

                $extensions['0'] = $this->lang->get('select');
                ksort($extensions);

                $this->view->set('domain_extensions', $extensions);

                $extra_form = $this->view->fetch('products/productTabs/domainForm.php');
                $product_type = 'domain';

            } elseif ($type && $type->is_hosting == '1') {
                // check we have a server group before proceeding
                $servers = Server::formattedList(
                    'id',
                    'name',
                    array(
                        array(
                            'column' => 'is_active',
                            'operator' => '=',
                            'value' => 1
                        )
                    )
                );

                if (empty($servers)) {
                    App::get('session')->setFlash('error', $this->lang->get('no_server_groups_exist'));
                    return $this->redirect('admin-product');
                }

                $extra_form = $this->view->fetch('products/productTabs/hostingForm.php');
                $product_type = 'hosting';

            } elseif ($product_type->addon_id > 0) {
                // Product type is handled by an addon

                // Attempt to load the addon
                $addon = \Addon::find($product_type->addon_id);
                $addon_details = $addon->details();

                $addon_cameled = Str::studly($addon->directory);

                // Load the addon product types handler
                $product_type_helper = \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\ProductTypesHelper');

                $addon_tabs = $product_type_helper->loadProductTabs($product_type);

                if (is_array($addon_tabs)) {
                    $this->view->set('extra_tabs', $addon_tabs);

                    // Load addon view data
                    $addon_view_data = $product_type_helper->loadViewData($product_type);

                    if (is_array($addon_view_data) && ! empty($addon_view_data)) {
                        foreach ($addon_view_data as $field => $value) {
                            $this->view->set($field, $value);
                        }
                    }

                    // Load addon form data
                    $addon_form_data = $product_type_helper->loadFormData($product_type);

                    if (is_array($addon_form_data) && ! empty($addon_form_data)) {
                        foreach ($addon_form_data as $field => $value) {
                            \Whsuite\Inputs\Post::set($field, $value);
                        }
                    }
                }

                // These wont be needed.
                $extra_form = null;
                $product_type = null;

            } else {
                $extra_form = null;
                $product_type = null;
            }

            $this->view->set('extra_form', $extra_form);
            $this->view->set('product_type', $product_type);

            $this->view->set('billing_periods', BillingPeriod::orderByRaw('sort = 0, sort ASC')->get());
            $this->view->set('currencies', Currency::all());

            return $this->view->display('products/newProduct/step2.php');
        }

        $title = $this->lang->get('new_product_step_one');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('group', $group);
        $this->view->set('product_types', ProductType::formattedList('id', 'name'));

        $this->view->display('products/newProduct/step1.php');
    }

    /**
     * Addon Fields
     *
     * Loads the addon fields via ajax when a server group is picked.
     * As default this finds the most relevent server, connects to it and pulls any details needed
     * however this is completely handled by the server module so can actually work
     * differently if needed.
     *
     * @param id Product Group ID to add the product to
     */
    public function addonFields($id, $product_id = null)
    {
        $group = ServerGroup::find($id);
        if (empty($group)) {
            return $this->lang->get('an_error_occurred');
        }

        if ($group->server_module_id < 1) {
            return null;
        }

        $server_module = $group->ServerModule()->first();
        $addon = $server_module->Addon()->first();

        // Pick the most relevant server.
        $server_helper = App::factory('\App\Libraries\ServerHelper');
        $server = $server_helper->defaultServer($id);
        $server_helper->initAddon($server->id);

        if ($server->id > 0) {
            if ($product_id > 0) {
                $product = \Product::find($product_id);
                $product_data = $product->ProductData()->get();

                $package_meta = array();
                foreach ($product_data as $data) {
                    $package_meta[$data->slug] = $data->value;
                }
                \Whsuite\Inputs\Post::set('PackageMeta', $package_meta);

            }
            $this->assets->addScript('application.min.js');

            $this->view->set('fields', $server_helper->productFields());

            $this->view->display('products/productTabs/addonFieldsAjax.php');
        }
    }

    /**
     * Registrar Fields
     *
     * Loads the addon fields via ajax when a domain extension is picked.
     *
     * @param id Product Group ID to add the product to
     */
    public function registrarFields($id)
    {
        $extension = DomainExtension::find($id);
        if (empty($extension)) {
            return $this->lang->get('an_error_occurred');
        }

        if ($extension->registrar_id < 1) {
            return null;
        }

        $registrar = $extension->Registrar()->first();
        $addon = $registrar->Addon()->first();

        App::factory('Addon\\'.$addon->directory.'\Libraries\\'.$addon->directory)->productFields($id);
    }


    public function hostingValidation($product_data)
    {
        // We've not got anything that needs validating here yet, however the
        // method should remain in place for consistency and to allow for any
        // future validation here.
        return array(
            'result' => 'success',
            'errors' => array()
        );
    }

    public function domainValidation($product_data, $domain_data)
    {
        // We've not got anything that needs validating here yet, however the
        // method should remain in place for consistency and to allow for any
        // future validation here.
        return array(
            'result' => 'success',
            'errors' => array()
        );
    }

    public function domainPricing($id)
    {
        $extension = DomainExtension::find($id);
        if (empty($extension)) {
            return $this->lang->get('an_error_occurred');
        }

        $this->view->set('extension', $extension);
        $this->view->set('currencies', Currency::all());
        $this->view->display('products/productTabs/domainPricingDetails.php');
    }

    public function manageProduct($id, $product_id)
    {
        $group = ProductGroup::find($id);
        $product = Product::find($product_id);

        if (empty($group) || empty($product) || $group->id != $product->product_group_id) {
            return $this->redirect('admin-product');
        }

        $currencies = Currency::all();

        $post_data = \Whsuite\Inputs\Post::get();

        if (isset($post_data['submit'])) {
            // This is the final stage. At this point we're going to create the
            // product assuming all the data was entered correctly.

            // Validation here is a bit complex, and we need somewhere to store
            // the validation messages so we can return them all at once.
            $validation_data = array(
                'result' => 'success',
                'errors' => array()
            );

            // Merge the two arrays
            $product_data = $post_data['Product'];
            if (isset($post_data['PackageMeta'])) {
                $meta_data = $post_data['PackageMeta'];
            } else {
                $meta_data = array();
            }
            if (isset($post_data['Pricing'])) {
                $pricing_data = $post_data['Pricing'];
            } else {
                $pricing_data = array();
            }
            $product_type = ProductType::find($product_data['product_type_id']);
            if (empty($product_type)) {
                return $this->redirect('admin-product');
            }

            // Work out which addon we're using here.
            $addon_id = 0;

            if (isset($product_data['server_group']) && $product_data['server_group'] > 0) {
                $server_group = ServerGroup::find($product_data['server_group']);

                if (!empty($server_group) && $server_group->server_module_id > 0) {
                    $server_module = $server_group->ServerModule()->first();

                    if ($server_module->addon_id > 0) {
                        $addon = $server_module->Addon()->first();
                        $addon_id = $addon->id;
                    }
                }
            }

            // Set the post data again, so that if needed it can be re-used. This
            // is mainly if the data fails validation.
            \Whsuite\Inputs\Post::set('Product', $product_data);
            \Whsuite\Inputs\Post::set('PackageMeta', $meta_data);
            \Whsuite\Inputs\Post::set('Pricing', $pricing_data);

            // Validate the product data
            $validator = $this->validator->make($product_data, Product::$rules);

            if ($validator->fails()) {
                $validation_data['result'] = 'error';
                $validation_data['errors']  = $validation_data['errors'] + $validator->messages();
            }

            $product->product_type_id = $product_data['product_type_id'];
            $product->product_group_id = $group->id;
            $product->name = $product_data['name'];
            $product->description = $product_data['description'];
            $product->setup_automatically = 1;
            $product->is_active = $product_data['is_active'];
            $product->is_visible = $product_data['is_visible'];
            $product->email_template_id = $product_data['email_template_id'];
            $product->stock = $product_data['stock'];
            $product->auto_suspend_days = $product_data['auto_suspend_days'];
            $product->suspend_email_template_id = $product_data['suspend_email_template_id'];
            $product->auto_terminate_days = $product_data['auto_terminate_days'];
            $product->terminate_email_template_id = $product_data['terminate_email_template_id'];
            $product->is_taxed = $product_data['is_taxed'];
            $product->is_free = 0;
            $product->sort = $product_data['sort'];

            if ($product_type->is_hosting == '1') {
                // Because we cant validate pricing data very easily, we're going to
                // basically loop through each pricing record, and run it against the
                // validation, compiling a bunch of validation messages should we find
                // any issues.

                foreach ($pricing_data as $billing_period_id => $data) {
                    foreach ($data as $currency_id => $price_data) {
                        if (! empty($price_data['price'])) {
                            $compiled_pricing_data = array(
                                'product_id' => '0',
                                'currency_id' => $currency_id,
                                'billing_period_id' => $billing_period_id,
                                'price' => $price_data['price'],
                                'renewal_price' => $price_data['renewal_price'],
                                'setup' => $price_data['setup'],
                                'bandwidth_overage_fee' => $price_data['bandwidth_overage_fee'],
                                'diskspace_overage_fee' => $price_data['diskspace_overage_fee'],
                                'allow_in_signup' => $price_data['allow_in_signup']
                            );
                            $pricing_validator = $this->validator->make($compiled_pricing_data, ProductPricing::$rules);

                            if ($pricing_validator->fails()) {
                                $validation_data['result'] = 'error';
                                $validation_data['errors'] + $pricing_validator->messages();
                            }
                        }
                    }
                }

                // Now we've got to not only check the product type, but validate it.
                // This cant realistically be done here, so we've got two seperate
                // methods for both hosting and domains. One does validation, the other
                // does any inserts.

                $product->domain_type = $product_data['domain_type'];
                $product->charge_disk_overages = $product_data['charge_disk_overages'];
                $product->charge_bandwidth_overages = $product_data['charge_bandwidth_overages'];
                $product->allow_ips = $product_data['allow_ips'];
                $product->included_ips = $product_data['included_ips'];
                $product->allow_upgrade = 0;
                $product->upgrade_package_ids = '';
                $product->upgrade_email_template_id = 0;

                $hosting_validation = $this->hostingValidation($product_data);

                if ($hosting_validation['result'] == 'error') {
                    $validation_data['result'] = 'error';
                    $validation_data['errors'] + $hosting_validation['errors'];
                }
            } elseif ($product_type->is_domain == '1') {
                $domain_data = array();
                if (isset($post_data['Domain'])) {
                    $domain_data = $post_data['Domain'];
                }
                \Whsuite\Inputs\Post::set('Domain', $domain_data);

                $domain_validation = $this->domainValidation($product_data, $domain_data);

                if ($domain_validation['result'] == 'error') {
                    $validation_data['result'] = 'error';
                    $validation_data['errors'] + $domain_validation['errors'];
                }

                $domain_extension = $product->DomainExtension()->first();
                if (empty($domain_extension)) {
                    return $this->redirect('admin-product');
                }
            } elseif ($product_type->addon_id > 0) {
                // Product type is managed by an addon - we'll allow the addon
                // to have a copy of all processed data here so it can perform
                // any actions it needs to.
                $addon = \Addon::find($product_type->addon_id);

                $addon_details = $addon->details();

                $addon_cameled = Str::studly($addon->directory);

                // Load the addon product types handler
                $product_type_helper = \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\ProductTypesHelper');

                $validate_addon_tabs  = $product_type_helper->validateAddonProductData($product_type, $post_data);

                if (is_array($validate_addon_tabs) && ! empty($validate_addon_tabs)) {
                    $validation_data = $validate_addon_tabs;
                }
            }

            if ($validation_data['result'] == 'success') {
                // Update product
                $product_status = $product->save();
                // As default the pricing status is true as it's possible to not
                // have any price records at all if the product is free.
                $pricing_status = true;

                // If applicable, update domain extension
                if (isset($domain_extension)) {
                    $domain_extension->product_id = $product->id;
                    $domain_extension->save();
                }

                if ($product_type->is_domain == '1') {
                    // Delete the existing pricing data
                    $existing_pricing = DomainPricing::where('domain_extension_id', '=', $domain_extension->id)->delete();

                    foreach ($pricing_data as $billing_period => $data) {
                        foreach ($data as $currency_id => $price_data) {
                            if (! empty($price_data['registration'])) {
                                $domain_pricing = new DomainPricing();
                                $domain_pricing->domain_extension_id = $domain_extension->id;
                                $domain_pricing->years = $billing_period;
                                $domain_pricing->currency_id = $currency_id;
                                $domain_pricing->registration = $price_data['registration'];
                                $domain_pricing->renewal = $price_data['renewal'];
                                $domain_pricing->transfer = $price_data['transfer'];
                                $domain_pricing->restore = $price_data['restore'];

                                $pricing_status = $domain_pricing->save();
                            }
                        }
                    }
                } elseif ($product_type->addon_id > 0) {
                    $addon_details = $addon->details();

                    $addon_cameled = Str::studly($addon->directory);

                    // Load the addon product types handler
                    $product_type_helper = \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\ProductTypesHelper');

                    $save_addon_data  = $product_type_helper->saveAddonProductData($product_type, $post_data, $product);
                } else {
                    // Delete the existing pricing data
                    $existing_pricing = ProductPricing::where('product_id', '=', $product->id)->delete();

                    // Insert product pricing
                    foreach ($pricing_data as $billing_period_id => $data) {
                        foreach ($data as $currency_id => $price_data) {
                            if (! empty($price_data['price'])) {
                                if (isset($pricing_status) && $pricing_status == false) {
                                    // An error occurred at some point when adding
                                    // one of the pricing data records, skip over
                                    // the rest so we can show an unexpected error.
                                    continue;
                                }

                                $pricing_record = new ProductPricing();
                                $pricing_record->product_id = $product->id;
                                $pricing_record->currency_id = $currency_id;
                                $pricing_record->billing_period_id = $billing_period_id;
                                $pricing_record->price = $price_data['price'];
                                $pricing_record->renewal_price = $price_data['renewal_price'];
                                $pricing_record->setup = $price_data['setup'];
                                $pricing_record->bandwidth_overage_fee = $price_data['bandwidth_overage_fee'];
                                $pricing_record->diskspace_overage_fee = $price_data['diskspace_overage_fee'];
                                $pricing_record->allow_in_signup = $price_data['allow_in_signup'];

                                $pricing_status = $pricing_record->save();
                            }
                        }
                    }
                }

                if ($product_type->addon_id <= 0) {
                    // Delete existing meta data (if any)
                    $existing_data = ProductData::where('product_id', '=', $product->id)->delete();

                    // If applicable, add product meta data
                    foreach ($meta_data as $slug => $value) {
                        $metadata = new ProductData();
                        $metadata->product_id = $product->id;
                        $metadata->addon_id = $addon_id;
                        $metadata->slug = $slug;
                        $metadata->value = $value;
                        $metadata->is_encrypted = 0;
                        $metadata->is_array = 0;

                        $metadata->save();
                    }
                }

                if ($product_status && $pricing_status) {
                    // All good!
                    App::get('session')->setFlash('success', $this->lang->get('product_updated'));
                    return $this->redirect('admin-product-manage', ['id' => $group->id, 'product_id' => $product->id]);
                } else {
                    // Something went wrong with the update, but we dont really know what.
                    \App\Libraries\Message::set($this->lang->get('an_error_occurred'), 'fail');
                }
            } else {
                \App\Libraries\Message::set($this->lang->formatErrors($validation_data['errors']), 'fail');
            }
        }

        \Whsuite\Inputs\Post::set('Product', $product->toArray());
        // Set a null addon var so we dont get errors later down the line
        $addon = null;

        // Load the product type as we need to do some extra work if its a domain
        // or hosting product that has a registrar/server module addon
        $type = $product->ProductType()->first();
        $product_type = 'other';

        // Now load up any product data that we might have stored for an
        // addon module. This will be for things like remote-server package names
        // and such.
        $product_data = $product->ProductData()->get();
        $this->view->set('product_data', $product_data);

        $package_meta = array();
        foreach ($product_data as $data) {
            $package_meta[$data->slug] = $data->value;
        }
        \Whsuite\Inputs\Post::set('PackageMeta', $package_meta);

        // If the product type is a domain, we need to get the domain pricing,
        // extension and the registrar details (if any).
        if ($type->is_domain == '1') {
            $product_type = 'domain';

            // Load up the domain extension and it's pricing
            $domain_extension = $product->DomainExtension()->first();
            $pricing = $domain_extension->DomainPricing()->get();

            //Add the extension to the view (we'll add the pricing a bit later)
            $this->view->set('extension', $domain_extension);
            \Whsuite\Inputs\Post::set('Domain', $domain_extension->toArray());

            // If the domain extension is linked to a registrar module we need to
            // load up that module. We then check to see if that registrar had an
            // addon in the system (which it likely will) and then load that as well.
            // We wont actually add the addon var into the view yet - that gets
            // done furthur down.
            if ($domain_extension->registrar_id > 0) {
                $registrar = $domain_extension->Registrar()->first();
                $this->view->set('registrar', $registrar);
                if ($registrar->addon_id > 0) {
                    $addon = $registrar->Addon()->first();
                }
            }
            $this->view->set('addon', $addon);

            $extra_form = $this->view->fetch('products/productTabs/domainFormManage.php');

            // For the domain pricing we need to build up an array of the pricing
            // values stored for each currency. We then set this into the post data
            // and it will then populate the form.

            $pricing_data = array();
            foreach ($pricing as $price) {
                $currency = Currency::find($price->currency_id);
                $pricing_data[$price->years][$price->currency_id] = array(
                    'registration' => App::get('money')->format($price->registration, $currency->code, true),
                    'renewal' => App::get('money')->format($price->renewal, $currency->code, true),
                    'transfer' => App::get('money')->format($price->transfer, $currency->code, true),
                    'restore' => App::get('money')->format($price->restore, $currency->code, true)
                );
            }
            \Whsuite\Inputs\Post::set('Pricing', $pricing_data);

        } elseif ($type->addon_id > 0) {
            // Product type is handled by an addon

            // Attempt to load the addon
            $addon = \Addon::find($type->addon_id);
            $addon_details = $addon->details();

            $addon_cameled = Str::studly($addon->directory);

            // Load the addon product types handler
            $product_type_helper = \App::factory('\Addon\\' . $addon_cameled . '\\Libraries\ProductTypesHelper');

            $addon_tabs = $product_type_helper->loadProductTabs($type, $product);

            if (is_array($addon_tabs)) {
                $this->view->set('extra_tabs', $addon_tabs);

                // Load addon view data
                $addon_view_data = $product_type_helper->loadViewData($type, $product);

                if (is_array($addon_view_data) && ! empty($addon_view_data)) {
                    foreach ($addon_view_data as $field => $value) {
                        $this->view->set($field, $value);
                    }
                }

                // Load addon form data
                $addon_form_data = $product_type_helper->loadFormData($type, $product);

                if (is_array($addon_form_data) && ! empty($addon_form_data)) {
                    foreach ($addon_form_data as $field => $value) {
                        \Whsuite\Inputs\Post::set($field, $value);
                    }
                }

            }
            // These wont be needed.
            $extra_form = null;
            $product_type = 'addon';
            $pricing = null;


        } else {
            // If the product type is not a domain, we just use the standard
            // pricing system, however we've still got some extra work that needs
            // to be done if its a hosting package as it'll likely have a server
            // module
            if ($type->is_hosting == '1') {
                $product_type = 'hosting';

                // Load the server group for this product
                $server_group = $product->ServerGroup()->first();
                $server_module = null;
                if ($server_group && $server_group->server_module_id > 0) {
                    // If the server group has a server module assigned to it,
                    // load that up too
                    $server_module = $server_group->ServerModule()->first();

                    if ($server_module && $server_module->addon_id > 0) {
                        // Then finally if the server module has an addon (which
                        // in 99% of cases it will do) we load that up
                        $addon = $server_module->Addon()->first();
                    }
                }

                if (!empty($server_group)) {
                    \Whsuite\Inputs\Post::set('ServerGroup', $server_group->toArray());
                }

                $this->view->set('addon', $addon);

                // Work out the default server
                $server_id = null;

                if ($server_group && isset($server_module) && $server_module) {
                    $server_helper = App::factory('\App\Libraries\ServerHelper');
                    $server = $server_helper->defaultServer($server_group->id);
                }

                if (isset($server->id) && $server->id > 0) {
                    $this->view->set('server_id', $server->id);
                } else {
                    $this->view->set('server_id', false);
                }

                // Get the domain types
                $domain_types = array();
                foreach (Product::$product_domain_options as $id => $type) {
                    $domain_types[$id] = $this->lang->get($type);
                }
                $this->view->set('domain_types', $domain_types);



                // To finish off, add the server group and module, as well as the product fields to the views
                $this->view->set('server_group', $server_group);
                $this->view->set('server_module', $server_module);

                $product_id = 0;

                if (isset($product->id) && $product->id > 0) {
                    $product_id = $product->id;
                }

                $this->view->set('product_id', $product_id);

                $extra_form = $this->view->fetch('products/productTabs/hostingFormManage.php');
            }
            // Since we're using either a hosting product or some sort of other
            // product, we just use the standard pricing system
            $pricing = $product->ProductPricing()->get();
            $pricing_data = array();
            foreach ($pricing as $price) {
                $currency = Currency::find($price->currency_id);
                $pricing_data[$price->billing_period_id][$price->currency_id] = array(
                    'price' => App::get('money')->format($price->price, $currency->code, true),
                    'renewal_price' => App::get('money')->format($price->renewal_price, $currency->code, true),
                    'setup' => App::get('money')->format($price->setup, $currency->code, true),
                    'bandwidth_overage_fee' => App::get('money')->format($price->bandwidth_overage_fee, $currency->code, true),
                    'diskspace_overage_fee' => App::get('money')->format($price->diskspace_overage_fee, $currency->code, true),
                    'allow_in_signup' => App::get('money')->format($price->allow_in_signup, $currency->code, true)
                );
            }
            \Whsuite\Inputs\Post::set('Pricing', $pricing_data);

        }
        // At this point we'll have our pricing, and if applicable an addon
        // These get added to the view for later use
        $this->view->set('pricing', $pricing);

        $this->view->set('product_type', $product_type);

        $this->view->set('product', $product);
        $this->view->set('group', $group);
        $this->view->set('currencies', $currencies);

        $this->view->set('billing_periods', BillingPeriod::orderByRaw('sort = 0, sort ASC')->get());

        if ($product_type == 'domain') {
            $pricing_form = $this->view->fetch('products/productTabs/domainPricingDetails.php');
        } elseif ($product_type != 'addon') {
            $pricing_form = $this->view->fetch('products/productTabs/productPricingDetails.php');
        }

        if ($product_type != 'addon') {
            $this->view->set('pricing_form', $pricing_form);


            // If we've got an extra form for hosting or domains, add that to the view
            if (isset($extra_form)) {
                $this->view->set('extra_form', $extra_form);
            }
        }

        // Build the product setup options array and add it to the view.
        $product_setup_options = array();

        foreach (Product::$product_setup_options as $id => $value) {
            $product_setup_options[$id] = $this->lang->get($value);
        }

        $this->view->set('product_setup_options', $product_setup_options);

        // Add the email templates list to the view.
        $this->view->set('email_templates', EmailTemplate::formattedList('id', 'name'));

        // Get a count of how many purchases use this product. This is used to show
        // or hide the delete button (its only shown if the product is not in use)
        $this->view->set('purchase_count', $product->ProductPurchase()->count());

        // Now do the basic stuff - set the title, breadcrumbs, etc and return the
        // main view. Magic!

        $title = $this->lang->get('manage_product').' - '.$product->name;

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('product_management'), 'admin-product');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

        $this->view->display('products/manageProduct.php');
    }

    public function deleteProduct($id, $product_id)
    {
        $group = ProductGroup::find($id);
        $product = Product::find($product_id);

        if (empty($group) || empty($product) || $group->id != $product->product_group_id) {
            return $this->redirect('admin-product');
        }

        $purchases = $product->ProductPurchase()->get();
        if ($purchases->count() > 0) {
            return $this->redirect('admin-product');
        }

        $product_type = $product->ProductType()->first();

        if ($product_type->is_domain == '1') {
            $extension = $product->DomainExtension()->first();
            // Delete domain pricing
            $extension->DomainPricing()->delete();

            // Release the domain extension
            $extension->product_id = 0;
            $extension->save();
        } else {
            // Delete Pricing
            $product->ProductPricing()->delete();
        }

        // Delete metadata (although there shouldn't be any!)
        $product->ProductData()->delete();

        // Delete product addon pivot links
        $product->ProductAddonProduct()->delete();

        if ($product->delete()) {
            App::get('session')->setFlash('success', $this->lang->get('product_deleted'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('an_error_occurred'));
        }

        return $this->redirect('admin-product');
    }
}
