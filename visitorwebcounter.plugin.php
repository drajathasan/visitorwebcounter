<?php
/**
 * Plugin Name: VisitorWebCounter
 * Plugin URI: https://github.com/drajathasan/visitorWebCounter/
 * Description: Untuk counting
 * Version: 1.0.0
 * Author: Drajat Hasan
 * Author URI: https://github.com/drajathasan/
 */

// local vendor
require __DIR__ . DS . 'vendor' . DS . 'autoload.php';

// Load dependencies
use SLiMS\DB;
use WhichBrowser\Parser as BrowserParser;

// get plugin instance
$plugin = \SLiMS\Plugins::getInstance();

// registering menus
$plugin->registerMenu('reporting', 'Visitor Web Counter', __DIR__ . '/index.php');
$plugin->registerMenu('opac', 'visitorcounterrest', __DIR__ . '/visitorcounterrest.php');

// Hook
$plugin->register('after_content_load', function() {

    if (!isset($_SESSION['webVisitor']))
    {
        $_SESSION['webVisitor'] = utility::createRandomString(5);
    }
    
    // browser detetor
    $Detector = new BrowserParser(getallheaders(), [ 'detectBots' => false ]);
    
    
    if (!empty($Detector->browser->toString()))
    {
        $DB = DB::getInstance();
        $StoreData = [
            'activity' => array_key_first($_GET),
            'input' => NULL,
            'querystring' => NULL,
            'inputdate' => date('Y-m-d H:i:s'),
            'uniqueuserid' => $_SESSION['webVisitor']
        ];
    
        switch ($StoreData['activity']) {
            case 'search':
                $StoreData['input'] = (!empty($_GET['keywords'])) ? urldecode($_GET['keywords']) : null;
                if (isset($_GET['searchtype']) && $_GET['searchtype'] == 'advance')
                {
                    $StoreData['activity'] = 'Advance';
                }
                break;
            
            case 'p':
                $StoreData['activity'] = 'page';
                $StoreData['input'] = trim($_GET['p']);
                if (isset($_GET['keywords']) && !empty($_GET['keywords']))
                {
                    $StoreData['input'] = trim($_GET['p']) . '+' . trim(urldecode($_GET['keywords']));
                }
                break;
        }
    
        // encode get as query string in json
        $StoreData['querystring'] = count($_GET) ? json_encode(array_slice($_GET, 1)) : null;
    
        // set store state
        $StoreState = $DB->prepare('insert into vistor_log set uniqueuserid = :uniqueuserid, activity = :activity, input = :input, querystring = :querystring, inputdate = :inputdate');
        $StoreState->execute($StoreData);
    }
});
