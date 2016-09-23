<?php

namespace App\Libraries;

class ReportHelper
{
    public function generateCsv($data, $filename)
    {
        $lang = \App::get('translation');

        $output = array();
        $header = array();
        $header_row = array();

        foreach ($data as $row) {
            $output[] = '"'.implode('","', $row).'"';

            $header_row = array_keys($row);
        }

        foreach ($header_row as $column) {
            $header[] = $lang->get($column);
        }

        $csv_data = implode(",", $header)."\n";
        $csv_data .= implode("\n", $output);

        $Http = new \Whsuite\Http\Http;
        $Response = $Http->newResponse();

        $Response->setHeaders(
            array(
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Type' => 'text/csv',
                'Content-Length' => strlen($csv_data),
                'Content-Disposition' => 'attachment; filename=' . $filename
            )
        );
        $Response->setContent($csv_data);

        $Http->send($Response);
    }

    public function removeArrayItems(&$array, $keys)
    {

        foreach ($array as $key => &$value) {
            if (is_array($value) && ! empty($value)) {
                $array[$key] = $this->removeArrayItems($value, $keys);
            } else {
                if (in_array($key, $keys)) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }
}
