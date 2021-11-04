<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-10-29 21:36:01
 * @modify date 2021-10-29 21:36:01
 * @desc [description]
 */

use SLiMS\DB;

defined('INDEX_AUTH') OR die('Direct access not allowed!');

$CachePath = UPLOAD . 'cache' . DS . 'cacheVisitorCounter.json';

require __DIR__ . '/helper.php';

if (isCacheValid()) jsonResponse(file_get_contents($CachePath), false); 

// Create instance
$Instance = DB::getInstance();

// Today
$TodayState = $Instance->prepare('select count(uniqueuserid) from `vistor_log` where substring(inputdate, 1,10) = ? group by uniqueuserid');
$TodayState->execute([date('Y-m-d')]);
$Today = $TodayState->rowCount();

// Week
$WeekState = $Instance->prepare('select count(uniqueuserid) from `vistor_log` where (substring(inputdate, 1,10) >= ? and substring(inputdate, 1,10) <= ?) group by uniqueuserid');
$WeekState->execute([date('Y-m-d', strtotime('-7 days')), date('Y-m-d')]);
$Week = $WeekState->rowCount();

// Month
// cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'))
$FirstDay = date('Y-m-d', strtotime('-30 day'));
$LastDay = date('Y-m-d');
$WeekState->execute([$FirstDay, $LastDay]);
$Month = $WeekState->rowCount();

// All
$AllAccess = $Instance->query('select count(uniqueuserid) from `vistor_log` group by uniqueuserid');
$All = $AllAccess->rowCount();

jsonResponse(createCache(['expire' => strtotime((date('Y-m-d H:i:s', strtotime('+5 minutes')))), 'all' => $All, 'today' => $Today, 'week' => $Week, 'month' => $Month]));
