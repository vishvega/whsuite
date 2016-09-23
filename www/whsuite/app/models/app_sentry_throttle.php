<?php

use \Cartalyst\Sentry\Throttling\Eloquent\Throttle as SentryThrottle;

class AppSentryThrottle extends SentryThrottle
{
    /**
     * storage for model_fields if getSchema is called
     *
     * @var array
     */
    static $model_fields = null;

    /**
     * create a paginated array of data
     *
     * @param   int         Number of items per page
     * @param   int         Page number
     * @param   array       Conditions to search on
     * @param   string|bool Field to sort on
     * @param   string      Sort direction
     * @param   string      Route name to use for paging links
     * @param   array       array of data to be passed to route generator
     * @return  array       array of results
     */
    public static function paginate($per_page, $page, $conditions = array(), $sort_by = false, $sort_order = 'desc', $route = null, $params = array())
    {
        $instance = new static;
        $query = $instance->newQuery();

        if (!empty($conditions)) {

            foreach ($conditions as $condition) {

                if (
                    isset($condition['column']) &&
                    isset($condition['value'])
                ) {
                    if (isset($condition['type']) && $condition['type'] == 'in') {

                        $query->whereIn($condition['column'], $condition['value']);
                    } elseif (isset($condition['type']) && $condition['type'] == 'or') {

                        $query->orWhere($condition['column'], $condition['operator'], $condition['value']);
                    } else {

                        $query->where($condition['column'], $condition['operator'], $condition['value']);
                    }
                }
            }
        }

        if (is_array($sort_by)) {

            foreach ($sort_by as $field => $direction) {

                $query->orderBy($field, $direction);
            }
        } elseif ($sort_by !== false) {

            $query->orderBy($sort_by, $sort_order);
        }

        $total_rows = $query->count();
        $last_page = ceil($total_rows / $per_page);
        $first_page = 1;

        $result_start = ($page-1) * $per_page;

        $results = $query->take($per_page)->skip($result_start)->get();

        if (!$route) {
            $route = 'admin-'.strtolower(get_class($instance)).'-paging';
        }

        // Retain any additional query params not handled by the router (e.g
        // from forms that use get methods)
        $additional_params = null;
        if (! empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '&') !== false) {
            $query_params = explode("&", explode("?", $_SERVER['REQUEST_URI'])[1]);

            if (! empty($query_params)) {
                $additional_params = '?';

                foreach ($query_params as $param) {
                    $additional_params .= $param . '&';
                }
            }
        }

        $pagination_data = array(
            'pagination_current_page' => $page,
            'pagination_results_per_page' => $per_page,
            'pagination_total_rows' => $total_rows,
            'pagination_last_page' => $last_page,
            'pagination_route' => $route,
            'pagination_route_params' => $params,
            'pagination_additional_params' => $additional_params
        );
        App::get('view')->set($pagination_data);
        $pagination_view = App::get('view')->fetch('elements/pagination.php');
        App::get('view')->set('pagination', $pagination_view);

        return $results;
    }

    /**
     * get the column details for this model
     * will store in $model_fields to prevent repeat querying
     *
     * @return  array   array of columns and their info
     */
    public static function getSchema()
    {
        if (! is_null(static::$model_fields)) {

            return static::$model_fields;

        } else {

            $instance = new static;
            $data = $instance
                        ->select()
                        ->from('INFORMATION_SCHEMA.COLUMNS')
                        ->where('TABLE_SCHEMA', '=', App::get('configs')->get('database.mysql.name'))
                        ->where('TABLE_NAME', '=', $instance->getTable())
                        ->get();

            $fields = array();

            foreach ($data as $row) {

                $row = $row->toArray();

                $fields[$row['COLUMN_NAME']] = $row;
            }
            static::$model_fields = $fields;

           return $fields;
        }
    }
}
