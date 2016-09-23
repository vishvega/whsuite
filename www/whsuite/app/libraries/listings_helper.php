<?php

namespace App\Libraries;

class ListingsHelper
{
    /**
     * render a single field
     *
     * @param   object  $row    row from eloquent
     * @param   array   $row_array   elqouent row toArray'd
     * @param   array   $column     the column data from controller
     * @return  string  data to echo
     */
    public function singleField($row, $row_array, $column)
    {
        if (empty($row)) {

            return '-';
        }

        if (strpos($column['field'], '.') !== false) {

            if (! isset($column['separator'])) {

                $column['separator'] = ' ';
            }

            $to_display = $this->relatedData($row, $column);

            return implode($column['separator'], $to_display);
        } else {

            return $this->outputField($row, $column, $column['field']);
        }
    }

    /**
     * concatenate multiple fields into one column
     *
     * @param   object  $row    row from eloquent
     * @param   array   $row_array   elqouent row toArray'd
     * @param   array   $column     the column data from controller
     * @return  string  data to echo
     */
    public function multipleFields($row, $row_array, $column)
    {
        if (empty($row)) {

            return '-';
        }

        if (! isset($column['separator'])) {

            $column['separator'] = ' ';
        }

        $keys = array_fill_keys($column['field'], 1);
        $col_data = array();

        // loop the fields and get the related data if set
        foreach ($keys as $field => $val) {

            if (strpos($field, '.') !== false) {

                $tmp_column = $column;
                $tmp_column['field'] = $field;
                $col = $this->relatedData($row, $tmp_column);

            } else {

                $col = array_intersect_key($row_array, array($field => 1));
            }

            if (! empty($col)) {

                $col_data[] = array_shift($col);
            }
        }

        return implode($column['separator'], $col_data);
    }

    /**
     * concatenate multiple fields into one column
     *
     * @param   object  $row    row from eloquent
     * @param   array   $row_array   elqouent row toArray'd
     * @param   array   $column     the column data from controller
     * @param   array   $actions     the action data from controller
     * @return  string  data to echo
     */
    public function actionButton($row, $row_array, $column, $actions)
    {
        $button = $actions[$column['action']];
        $params = array();

        if (! empty($button['params'])) {

            $keys = array_fill_keys($button['params'], 1);
            $params = array_intersect_key($row_array, $keys);
        }

        $link = \App::get('router')->generate($button['url_route'], $params);

        $class = (! empty($button['link_class'])) ? ' class="' . $button['link_class'] . '"' : '';

        $output = '<a href="' . $link . '"' . $class . '>';

        if (! empty($button['icon'])) {
            $output .= '<i class="' . $button['icon'] . '"></i> ';
        }

        if (! empty($button['label'])) {
            $output .= \App::get('translation')->get($button['label']);
        }

        $output .= '</a>';

        return $output;
    }

    /**
     * output the field
     * more options can be added here when we need to output differently according to column type
     *
     * @param object $row Eloquent model object
     * @param array $column column data from the controller
     * @param string $field The field name we are dealing with
     * @return string The data to display
     */
    public function outputField($row, $column, $field)
    {
        if (empty($row)) {

            return '-';
        }

        if (isset($column['type'])) {

            if ($column['type'] == 'date' || $column['type'] == 'datetime') {

                return $this->dateOutput($row, $field, $column['type']);

            } elseif ($column['type'] == 'options') {

                return $this->optionsOutput($row, $field, $column);

            } else {

                return $row->$field;
            }

        } else {

            $model = get_class($row);
            $schema = $model::getSchema();

            if (isset($schema[$field])) {

                $col_data = $schema[$field];

                if (
                    $col_data['DATA_TYPE'] == 'timestamp' ||
                    $col_data['DATA_TYPE'] == 'datetime' ||
                    $col_data['DATA_TYPE'] == 'date'
                ) {

                    $date = 'date';
                    if ($col_data['DATA_TYPE'] == 'timestamp' || $col_data['DATA_TYPE'] == 'datetime') {

                        $type = 'datetime';
                    }
                    return $this->dateOutput($row, $field, $type);

                } elseif ($col_data['DATA_TYPE'] == 'tinyint') {

                    return $this->optionsOutput($row, $field, $column);
                }
            }
        }

        // if we get here, something's wrong, just return
        return $row->$field;
    }


    /**
     * get the related fields
     *
     * @param   object  $row  row from eloquent
     * @param   array   $column   colun data from the controller
     * @return  string  $data to echo
     */
    public function relatedData($row, $column)
    {
        $related = explode('.', $column['field']);
        $field = array_pop($related);

        return $this->getRelatedData($row, $related, $field, $column);
    }

    /**
     * recursively get the related data for sub relations
     *
     * @param   object  $row   row from eloquent
     * @param   array   $related    array of models to drill through to get related data
     * @param   string  $field  the field which we wish to echo in the end
     * @param   array   $column   colun data from the controller
     * @return  string  data to echo
     */
    public function getRelatedData($row, $related, $field, $column)
    {
        $model = array_shift($related);

        if (method_exists($row, $model)) {

            $related_data = $row->$model;

            if ($related_data instanceof \Illuminate\Database\Eloquent\Collection) {

                $related_data = $related_data->all();
            }

            if (! empty($related)) {

                $to_display = $this->getRelatedData($related_data, $related, $field, $column);

            } else {

                $to_display = array();

                // it's returned a single row, turn into an array to keep it easy
                if (! is_array($related_data)) {

                    $related_data = array($related_data);
                }

                foreach ($related_data as $related_row) {

                    $to_display[] = $this->outputField($related_row, $column, $field);
                }
            }
        }

        return $to_display;
    }

    /**
     * process a date / datetime / timestamp field
     *
     * @param object row object from the database
     * @param string field name we are showing
     * @param string type of date to show (date or datetime)
     * @return string data to output
     */
    protected function dateOutput($row, $field, $type)
    {
        if (! is_null($row->$field)) {

            $date = \Carbon\Carbon::parse(
                $row->$field,
                \App::get('configs')->get('settings.localization.timezone')
            );

            if ($type == 'datetime') {

                $format = \App::get('configs')->get('settings.localization.short_datetime_format');
            } else {

                $format = \App::get('configs')->get('settings.localization.short_date_format');
            }

            return $date->format($format);
        } else {

            return '-';
        }
    }

    /**
     * process a field with 'options', either flag type or multiple select options
     *
     * @param object row object from the database
     * @param string field name we are showing
     * @param array array from controller defining column data
     * @return string data to object
     */
    protected function optionsOutput($row, $field, $column)
    {
        if (! isset($column['option_labels'])) {

            $column['option_labels'] = array(
                '0' => \App::get('translation')->get('no'),
                '1' => \App::get('translation')->get('yes')
            );
        }

        if (isset($column['option_labels'][$row->$field])) {

            return $column['option_labels'][$row->$field];
        } else {

            return $row->$field;
        }
    }

}
