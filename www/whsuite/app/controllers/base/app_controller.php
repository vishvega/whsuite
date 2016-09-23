<?php
/**
 * App Base Controller
 *
 * The AppController provides the option of adding any code that may need to run
 * across all controllers. Becuse of this, the AdminController and ClientController
 * both extend the AppController.
 *
 * @package  WHSuite-Controllers
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
use \Core\Controller;
use \Illuminate\Support\Str;
use \Whsuite\Inputs\Post as PostInput;

class AppController extends Controller
{
    public $mail;
    public $validator;
    public $languages;
    public $lang;
    public $date;
    public static $return_on_success;

    public function onLoad()
    {
        $validator = new \Whsuite\Validator\Validator();
        $this->validator = $validator->init(DEFAULT_LANG);

        if (App::get('session')->hasFlash('success')) {
            \App\Libraries\Message::set(App::get('session')->getFlash('success'), 'success');
        }

        if (App::get('session')->hasFlash('error')) {
            \App\Libraries\Message::set(App::get('session')->getFlash('error'), 'fail');
        }

        $timezone = \App::get('configs')->get('settings.localization.timezone');
        if (! empty($timezone)) {
            date_default_timezone_set($timezone);
        }

        $date = array(
            'short_date' => App::get('configs')->get('settings.localization.short_date_format'),
            'short_datetime' => App::get('configs')->get('settings.localization.short_datetime_format'),
            'full_date' => App::get('configs')->get('settings.localization.full_date_format'),
            'full_datetime' => App::get('configs')->get('settings.localization.full_datetime_format'),
            'timezone' => $timezone
        );
        $this->view->set('date', $date);
        $this->date = $date;

        $this->view->set('settings', App::get('configs')->get('settings'));

        App::get('breadcrumbs')->init('elements/breadcrumbs.php');

        $this->lang = App::get('translation');
        $this->router = App::get('router');
    }

    /**
     * given the controller name (via get_class) work out what the model name should be and return
     *
     * @return string   model name
     */
    protected function getModel()
    {
        $model = str_replace('Controller', '', get_class($this));
        $model = Str::studly($model);
        return Str::singular($model);
    }

    /**
     * work out which base class the main class is extending so we can
     * get whether we are in admin or client
     *
     * @return string   section
     */
    protected function getSection()
    {
        $reflection = new ReflectionClass($this);
        $parent = $reflection->getParentClass()->name;
        $parent = str_replace('controller', '', strtolower($parent));

        return $parent;
    }

    /**
     * Scaffolding Functions
     */
    /**
     * index scaffolding functions
     *
     */

    /**
     * toolbar to show on the index page
     *
     * set icon / text elements to null to hide any you don't want
     * link_class can also be left blank if not required.
     */
    protected function indexToolbar()
    {
        return array();
    }

    /**
     * columns to show on index and the header text lang
     *
     * fields column can be an array of fields, these will be "imploded" using the separator element (will be a space if omitted)
     * text will be taken from field if omitted, set to null to hide header text
     */
    protected function indexColumns()
    {
        return array();
    }

    /**
     * actions to show in the index listing.
     * element key relates to the 'action' element in indexColumns array
     */
    protected function indexActions()
    {
        return array();
    }

    /**
     * generate the index breadcrumb trail, can override without redefining whole index functon
     *
     * @param string $model - the model name we are dealing with, used to generate the routes
     * @param string $page_title - the page title of the current page
     */
    protected function indexBreadcrumb($model, $page_title)
    {
        // prefix - either admin or client
        $prefix = $this->getSection();

        $breadcrumb = App::get('breadcrumbs');
        $breadcrumb->add($this->lang->get('dashboard'), $prefix . '-home');
        $breadcrumb->add($page_title);
        $breadcrumb->build();
    }

    /**
     * scaffolding index / listing
     */
    public function index($page = 1, $per_page = null)
    {
        // prefix - either admin or client
        $prefix = $this->getSection();

        // first get the model we will be dealing with
        $this->model = $model = $this->getModel();

        // get the sectin header language
        $page_title = $this->lang->get(strtolower($model) . '_management');

        // build the breadcrumb
        $this->indexBreadcrumb($model, $page_title);

        // if no per pages are set, set default
        if (empty($per_page)) {
            $per_page = App::get('configs')->get('settings.general.results_per_page');
        }

        // get the actual data
        $data = $model::paginate(
            $per_page,
            $page,
            (! empty($this->paginate['conditions'])) ? $this->paginate['conditions'] : array(),
            (! empty($this->paginate['sort_by'])) ? $this->paginate['sort_by'] : false,
            (! empty($this->paginate['sort_order'])) ? $this->paginate['sort_order'] : 'desc',
            (! empty($this->paginate['route'])) ? $this->paginate['route'] : null,
            (! empty($this->paginate['params'])) ? $this->paginate['params'] : array()
        );

        // set the variables
        $this->view->set(array(
            'data' => $data,
            'title' => $page_title,
            'toolbar' => $this->indexToolbar(),
            'columns' => $this->indexColumns(),
            'actions' => $this->indexActions()
        ));

        // check for overriding template
        if (empty($this->render_view)) {
            $this->render_view = 'scaffolding/index.php';
        }

        // check for overriding table template
        if (empty($this->render_view_tbl_header)) {
            $this->render_view_tbl_header = 'elements/listing/tableHeader.php';
        }
        if (empty($this->render_view_tbl_body)) {
            $this->render_view_tbl_body = 'elements/listing/tableBody.php';
        }

        $this->view->set(array(
            'tbl_header_tpl' => $this->render_view_tbl_header,
            'tbl_body_tpl' => $this->render_view_tbl_body
        ));

        // load the listings helper
        App::factory('\App\Libraries\ListingsHelper');

        // annnnnnd render.
        $this->view->display($this->render_view);
    }

    /**
     * form scaffolding functions
     *
     */

    /**
     * toolbar to show on the form page
     *
     * set icon / text elements to null to hide any you don't want
     * link_class can also be left blank if not required.
     *
     * as default will load the index toolbar
     *
     * @param object $main_model - the main model we are loading
     */
    protected function formToolbar($main_model)
    {
        return $this->indexToolbar();
    }

    /**
     * form fields
     *
     * All the fields to show in the form in a Model.field_name format. See docs for more details
     */
    protected function formFields()
    {
        return array();
    }

    /**
     * generate the index breadcrumb trail, can override without redefining whole index functon
     *
     * @param string $model - the model name we are dealing with, used to generate the routes
     * @param string $page_title - the page title of the current page
     */
    protected function formBreadcrumb($model, $page_title)
    {
        // prefix - either admin or client
        $prefix = $this->getSection();

        $breadcrumb = App::get('breadcrumbs');
        $breadcrumb->add($this->lang->get('dashboard'), $prefix . '-home');
        $breadcrumb->add($this->lang->get(strtolower($model) . '_management'), $prefix . '-' . strtolower($model));
        $breadcrumb->add($page_title);
        $breadcrumb->build();
    }

    /**
     * processData
     *
     * Function called as the form data is assigned to the model.
     * useful for any processing needed on the field
     *
     * @param string $field - the field name we are dealing with
     * @param mixed $data - the data we are assigning to the model
     * @param object $main_model - the main model we are saving (passed by reference so we can affect it!)
     * @return mixed - data to assign to the model
     */
    protected function processData($field, $data, &$main_model)
    {
        return $data;
    }

    /**
     * getExtraData
     *
     * function to allow call to get and assign any extra data needed on the form
     *
     * @param object $model Model object for the controller we are in
     */
    protected function getExtraData($model)
    {
    }

    /**
     * afterSave callback
     *
     * Called once the save was successful
     *
     * @param object $main_model - the main model we are saving (passed by reference so we can affect it!)
     */
    protected function afterSave(&$main_model)
    {
    }

    /**
     * scaffolding add / edit
     *
     */
    public function form($id = null)
    {
        // prefix - either admin or client
        $prefix = $this->getSection();

        // first get the model we will be dealing with
        $this->model = $model = $this->getModel();

        if (! empty($id)) {
            $action_type = 'edit';
            $params = array('id' => $id); // for generating page url later

        } else {
            $action_type = 'add';
            $params = null;
        }

        // post?
        $data = PostInput::get('data');

        if (! empty($data) && isset($_POST['data']) && ! empty($_POST['data'])) {
            // validate
            $rules = array();

            if (isset($model::$rules)) {
                $rules = $model::$rules;
            }

            $validator = $this->validator->make($data[$model], $rules);
            $model_object = new $model;
            $customFieldValidator = $model_object->validateCustomFields(false);

            if (! $validator->fails() && $customFieldValidator['result']) {
                // save data here

                $main_model = $model::find($id);
                if (empty($main_model)) {
                    $main_model = new $model;
                }

                $formFields = $this->formFields();
                foreach ($formFields as $field => $attr) {
                    if (! is_array($attr)) {
                        $field = $attr;
                    }

                    // check if it has the model name in it
                    if (Str::contains($field, '.')) {
                        $field_bits = explode('.', $field);
                        $field = array_pop($field_bits);
                    }

                    // check if it's in the data array, if so add to the model
                    if (array_key_exists($field, $data[$model])) {
                        // pass the data through the processData function to allow controllers to
                        // perform actions on the data before adding it to the model
                        $processedData = $this->processData($field, $data[$model][$field], $main_model);

                        if ($processedData !== false) {
                            $main_model->$field = $processedData;
                        }
                    }
                }

                // try and save the data
                if ($main_model->save() && $main_model->saveCustomFields(false)) {
                    // call the afterSave callback
                    $this->afterSave($main_model);

                    // success message
                    App::get('session')->setFlash('success', $this->lang->get('scaffolding_save_success'));

                    // check if we want to return on success, if we do return the items ID number
                    // TODO: Review all the scaffolding code to have better "return" options
                    if (isset(self::$return_on_success) && self::$return_on_success) {
                        return $main_model->id;
                    } else {
                        return $this->redirect($prefix . '-' . strtolower($model));
                    }

                } else {
                    \App\Libraries\Message::set($return_error = $this->lang->get('scaffolding_save_error'), 'fail');
                }
            } else {
                // check for main validation error messages
                $main_errors = $validator->messages();

                if (! empty($main_errors)) {
                    $main_errors = $main_errors->toArray();
                } else {
                    $main_errors = array();
                }

                // check for any custom fields that may have failed
                if (! empty($customFieldValidator['errors'])) {
                    $cf_errors = $customFieldValidator['errors']->toArray();
                } else {
                    $cf_errors = array();
                }

                // merge the two lists then set to template
                $error_list = $main_errors + $cf_errors;

                \App\Libraries\Message::set(
                    $return_error = $this->lang->formatErrors(
                        json_encode($error_list)
                    ),
                    'fail'
                );
            }

            // hack for scaffolding functionality
            // allows the overriding class to use this function process but return once done to prevent it
            // loading the rest of the page.
            if (isset($this->return_after_process) && $this->return_after_process) {
                return $return_error;
            }
        }

        // get the sectin header language
        $page_title = $this->lang->get(strtolower($model) . '_' . $action_type);

        // build the breadcrumb
        $this->formBreadcrumb($model, $page_title);

        // check for overriding template
        if (empty($this->render_view)) {
            $this->render_view = 'scaffolding/form.php';
        }

        // load the form helper
        App::factory('\App\Libraries\FormHelper');

        // only get the row from database if id has been set and if post data is empty
        if (! empty($id) && empty($data[$model][$model::getPK()])) {
            $data = $model::find($id);
            $post_data = $data->toArray();

            // get any post data already set
            $existing_data = PostInput::get('data.' . $model);
            if (is_array($existing_data)) {
                $post_data = array_merge($existing_data, $post_data);
            }

            // set to post data
            PostInput::set('data.' . $model, $post_data);
        }

        if (isset($data) && is_object($data)) {
            $model_object = $data;
        } else {
            $model_object = new $model;
        }

        // Get any extra data we may want to get
        $this->getExtraData($model_object);

        $this->view->set(array(
            'model_object' => $model_object,
            'title' => $page_title,
            'toolbar' => $this->formToolbar($model_object),
            'page_url' => $this->router->generate($prefix . '-' . strtolower($model) . '-' . $action_type, $params),
            'fields' => $this->formFields()
        ));

        // annnnnnd render.
        $this->view->display($this->render_view);
    }

    /**
     * scaffolding delete item
     *
     */
    public function delete($id)
    {
        // prefix - either admin or client
        $prefix = $this->getSection();

        $this->model = $model = $this->getModel();

        // firt check to see if delete action are valid via index listing actions
        $actions = $this->indexActions();

        if (isset($actions['delete'])) {
            $item = $model::find($id);

            if (! empty($item)) {
                if ($item->delete()) {
                    App::get('session')->setFlash('success', $this->lang->get('scaffolding_delete_success'));
                } else {
                    App::get('session')->setFlash('error', $this->lang->get('scaffolding_delete_error'));
                }
            }

        } else {
            App::get('session')->setFlash('error', $this->lang->get('scaffolding_delete_notallowed'));
        }

        return $this->redirect($prefix . '-' . strtolower($model));
    }

    /**
     * redirect method
     *
     * @param string $route Either the Aura Route name or url
     * @param array $params Optional params needed for a route
     * @param int $code Define optional status code
     *
     * @return void
     */
    public function redirect($route, $params = array(), $code = 302)
    {
        return \App::redirect($route, $params, $code);
    }
}
