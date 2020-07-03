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
            // Validate date format
            if ($this->validateDate($data['dispStartDate']) && $this->validateDate($data['dispEndDate'])) {
                $current = time();
                if ($current >= strtotime($data['dispStartDate']) && $current < strtotime($data['dispEndDate'])) {
                    $isValid = true;
                }
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

    public function validateDate($date)
    {
        $isValid = false;

        // Pattern check format date and time
        $pattern = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2]\d|3[0-1])$|' .
            '\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2]\d|3[0-1])\s(0\d|1\d|2[0-3]):([0-5]\d):([0-5]\d)$/';

        if (preg_match($pattern, trim($date)) === 1) {
            // Pattern above already check valid for time, so we just valid date again
            $date = preg_split('/\s/', trim($date))[0];
            list ($y, $m, $d) = preg_split('/-/', $date);
            if (checkdate($m, $d, $y)) {
                $isValid = true;
            }
        }

        return $isValid;
    }
}
