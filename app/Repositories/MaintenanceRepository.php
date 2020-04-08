<?php

namespace App\Repositories;

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
     * @param $data
     * @return bool
     */
    protected function validate($data)
    {
        $isValid = false;
        if (isset($data['dispStartDate']) && isset($data['dispEndDate'])) {
            $current = time();
            if ($current >= strtotime($data['dispStartDate']) && $current < strtotime($data['dispEndDate'])) {
                $isValid = true;
            }
        }

        // No start and end date to validate with current time, so return not valid
        return $isValid;
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
