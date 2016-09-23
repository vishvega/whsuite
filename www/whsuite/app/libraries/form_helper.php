<?php

namespace App\Libraries;

class FormHelper
{

    /**
     * work out what form type we want to show for the given field
     *
     * @param   string  $field     The field we want to get the type of
     * @return  array  an array of attributes for the form helper ('type' at the very least)
     */
    public function getType($field)
    {
        if (strpos($field, '.') === false) {

            // doesn't have any dots, don't know what model it is, just return textfield
            return 'text';
        }

        $field_bits = explode('.', $field);
        $field_name = array_pop($field_bits);
        $model = array_pop($field_bits);
        $model = ucfirst($model);

        // get the schema
        $schema = $model::getSchema();
        $instance = new $model;

        if (isset($schema[$field_name]) && isset($schema[$field_name]['DATA_TYPE'])) {

            $data_type = strtolower($schema[$field_name]['DATA_TYPE']);

            if ($data_type == 'int' && $field_name == $instance->getKeyName()) {

                return 'hidden';

            } elseif ($data_type == 'text'){

                return 'textarea';

            } elseif ($data_type == 'tinyint') {

                return 'checkbox';

            } elseif ($data_type == 'int' && strpos($field_name, '_id') !== false) {

                return 'select';

            } elseif ($field_name == 'password') {

                return 'password';

            } else {

                return 'text';
            }

        } else {

            return 'text';
        }
    }

    /**
     * getFieldName
     *
     * Explode out a Staff.first_name type field to get the actual field name
     *
     * @param string $field - the Model.field_name field
     * @return string - the actual field name
     */
    public function getFieldName($field)
    {
        $ex = explode('.', $field);
        return array_pop($ex);
    }

}
