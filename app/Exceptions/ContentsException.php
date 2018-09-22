<?php
/**
 * Created by PhpStorm.
 * User: ayumu
 * Date: 2017/10/25
 * Time: 20:57
 */

namespace App\Exceptions;
use Exception;

class ContentsException extends Exception
{
    public $statusCode;

    public function __construct(string $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function render()
    {
        $errors = config('errors');
        $message ='';
        if(array_key_exists($this->statusCode, $errors)) {
            $message = $errors[$this->statusCode];
        }
        return response()->json(
            [
                'status' => $this->statusCode,
                'message' => $message
            ],
            202
        );
    }

}