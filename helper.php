<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-10-29 21:45:55
 * @modify date 2021-10-29 21:45:55
 * @desc [description]
 */

function isCacheValid($CachePath)
{
    if (!file_exists($CachePath))
    {
        return false;
    }

    $Data = json_decode(file_get_contents($CachePath), true);

    if ($Data['expire'] > strtotime(date('Y-m-d H:i:s')))
    {
        return true;
    }

    return false;
}

function jsonResponse($mix, $encode = true)
{
    header('Content-Type: application/json');
    echo ($encode) ? json_encode($mix) : $mix;
    exit;
}

function createCache($data, $CachePath)
{
    if ($data['all'] > 100) file_put_contents($CachePath, json_encode($data));
    return $data;
}