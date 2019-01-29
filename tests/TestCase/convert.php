<?php
/**
 * Created by PhpStorm.
 * User: usuda
 * Date: 2019/01/28
 * Time: 13:48
 */

function dd($st){
    var_dump($st);
    exit();
}
function mmc200convert ()
{
    $dir = '../Data/tol/ms/resources/ap09mmc200/old';
    $newDir = '../Data/tol/ms/resources/ap09mmc200';

    foreach(glob($dir . '/*') as $file){
        if(is_file($file)){
            $baseName = basename($file);
            if ($baseName !== '1' &&
                $baseName !==  '2') {
                echo $file . "\n";
                $xml = simplexml_load_file($file);
                $csv = $xml->responseData;
                $replaceCsv = str_replace('114500,0,', '0,', $csv) ;
//            var_dump($replaceCsv);exit();
                $xml->responseData = urlencode(mb_convert_encoding($replaceCsv, "SJIS", "UTF-8"));
                $xml->asXml($newDir . '/' .$baseName);
            }
        }
    }
}

function mre001convert ()
{

    $dir = '../Data/tol/ms/resources/ap07mre001/old';
    $newDir = '../Data/tol/ms/resources/ap07mre001';
    foreach(glob($dir . '/*') as $file){
        if(is_file($file)){
            $baseName = null;
            $xml = null;
            $data = null;
            $newFormat = null;
            $baseName = basename($file);
            if ($baseName !== '1' &&
                $baseName !==  '2') {
                echo $file . "\n";
                $xml = simplexml_load_file($file);
                $data = json_decode(json_encode($xml->responseData), true);
                $newFormat =
                    [
                        'response' => [
                            'common' => [
                                'return_cd' => $data['response']['common']['return_cd']
                            ],
                            'detail' => [
                                'rentaltorokushinseistatus' => $data['response']['detail']['rentaltorokushinseistatus'],
                                'rentalkoshinshinseistatus' => $data['response']['detail']['rentalkoshinshinseistatus'],
                                'honninkakuninyohi' => $data['response']['detail']['honninkakuninyohi'],

                            ],
                        ]
                    ];
                $xml->responseData = urlencode(json_encode($newFormat));
                $xml->asXml($newDir . '/' .$baseName);
            }
        }
    }

}

// mre001convert();


cp 2 f2NMiQbgQ2sAR6VwylPen%2FGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 YC9Dk3IHc1sqIGUGBFxCwvGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 ELjrCnorPEOzMcoct6uLhfGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 n4vlx5%2FDJoKheOYYNFuPbvGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 uyfcqotdA3UEPq6FRhmutPGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 Ec%2BXPR9JqRMYZb%2B7OQcWTvGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 RTt6G3UbVxzZpIfs86T6avGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 XVK8OHWvANbEKsQqF4rGXfGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 yZYgLWBBk4RMHXsFaxb1xvGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 9v9W6VweqVmiIK2PyIx50%2FGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 UOCJDoHmRGMNGHNj9HXw8vGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 GeW6EXlYsG1oEqzoNIWfZ%2FGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 eNe27HGNMNHy%2BDavpLl3UfGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 KRaA4Dwrv5qt1harfz%2F6CvGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 N2GWGHu4TdIQCLrLBRov9PGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 xahcuu4%2B9y5Fsj%2BBXNMWkvGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 WAZN%2FD72oQuSuJPouaJpk%2FGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 Xle3jYSSMsFVCxlGQf83p%2FGEpF%2BOYv7wkTIdfk0qJlc%3D
cp 2 Y%2FJXhpmHjN5mvwNb%2Fp6bXPGEpF%2BOYv7wkTIdfk0qJlc%3D