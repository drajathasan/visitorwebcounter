<?php
/**
 * Plugin Name: VisitorWebCounter
 * Plugin URI: https://github.com/drajathasan/visitorWebCounter/
 * Description: Untuk counting
 * Version: 1.1.0
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
$plugin->register('before_content_load', function() {
    if (!isset($_SESSION['webVisitor']))
    {
        $_SESSION['webVisitor'] = utility::createRandomString(5);
    }
    
    // browser detetor
    $Detector = new BrowserParser(getallheaders(), [ 'detectBots' => true ]);
    // SLiMS Version
    $Version = str_replace(['v','.'], '', SENAYAN_VERSION_TAG);
    
    
    if (!empty($Detector->browser->toString()) && $Version >= '940')
    {
        $DB = DB::getInstance();
        $StoreData = [
            'activity' => 'opac',
            'input' => NULL,
            'querystring' => NULL,
            'inputdate' => date('Y-m-d H:i:s'),
            'uniqueuserid' => $_SESSION['webVisitor']
        ];
    
        switch (true) {
            case (isset($_GET['search'])):
                if (isset($_GET['keywords'])) {
                    if (empty($_GET['keywords'])) return;
                    $StoreData['activity'] = 'search';
                    $StoreData['input'] = urldecode($_GET['keywords']);
                } elseif (isset($_GET['title'])) {
                    $StoreData['activity'] = 'advance';
                }
                break;
            
            case (isset($_GET['p']) && !empty($_GET['p'])):
                $StoreData['activity'] = 'page';
                $StoreData['input'] = trim($_GET['p']);

                if (strpos($StoreData['input'], 'api') !== false) {
                    return;
                }

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
