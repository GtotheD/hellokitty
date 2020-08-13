<?php

namespace App\Repositories;

use Validator;

class MaintenanceRepository
{
    const MAINTENANCE_FILE_PATH = '/export/home/tol/tp/data/json/maintenance/maintenance.json';

    public $file = '';

    public function __construct($file_path = '')
    {
        if (empty($file_path)) {
            $file_path = self::MAINTENANCE_FILE_PATH;
        }
        $this->file = $file_path;
    }

    /**
     * Load maintenance data
     * @return array
     */
    public function loadMaintenanceData()
    {
        // Check file content
        if (!file_exists($this->file)) {
            return [];
        }

        $content = file_get_contents($this->file);
        if (strlen($content) === 0) {
            return [];
        }

        $data = json_decode($content, true);
        if (!$this->validate($data)) {
            return [];
        }
        return $this->dataResponse($data);
    }

    /**
     * Validate data
     *
     * @param array $data List data need validate
     *
     * @return bool
     */
    protected function validate($data)
    {
        // Custom validate multi format for datetime
        Validator::extend('date_multi_format', function ($attribute, $value, $formats) {
            // iterate through all formats
            foreach ($formats as $format) {
                // parse date with current format
                $parsed = date_parse_from_format($format, $value);
                // if value matches given format return true=validation succeeded
                if ($parsed['error_count'] === 0 && $parsed['warning_count'] === 0) {
                    return true;
                }
            }
            // value did not match any of the provided formats, so return false=validation failed
            return false;
        });

        // Validate date format Y-m-d
        $now = date('Y-m-d H:i:s');
        $validator = Validator::make(
                        $data,
                        [
                            'dispStartDate' => 'required|date_multi_format:Y-m-d,Y-m-d H:i:s|before_or_equal:' . $now,
                            'dispEndDate' => 'required|date_multi_format:Y-m-d,Y-m-d H:i:s|after:' . $now
                        ]
                    );
        return $validator->passes();
    }

    /**
     * @param $data
     * @return array
     */
    protected function dataResponse($data)
    {
        $response = [];
        $keys = ['title', 'text', 'endDate', 'caution', 'button'];
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $response[$key] = $data[$key];
            }
        }
        return $response;
    }
}
