<?php
/**
 * @Created by          : Drajat Hasan
 * @Date                : 2021-10-28 10:52:55
 * @File name           : index.php
 * @Sponsored by        : Perpustakaan ITT Purwokerto
 */

defined('INDEX_AUTH') OR die('Direct access not allowed!');

// IP based access limitation
require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-reporting');
// start the session
require SB . 'admin/default/session.inc.php';
// set dependency
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require MDLBS . 'reporting/report_dbgrid.inc.php';
require __DIR__ . '/VisitorCouterWebReport.php';
// end dependency

// privileges checking
$can_read = utility::havePrivilege('reporting', 'r');

if (!$can_read) {
    die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
}

function httpQuery($query = [])
{
    return http_build_query(array_unique(array_merge($_GET, $query)));
}

$page_title = 'Visitor Web Counter';

/* Action Area */
$reportType = [
    [0, 'Pilih'], 
    ['accessPage', 'Halaman di Akses'],
    ['perPage', 'Per Halaman']
];

$reportPage = [
    [0, 'Pilih'], 
    ['page', 'Page'],
    ['search', 'Search'],
    ['Advance', 'Advance Search'],
    ['other', 'Lain-Lain']
];

$OptTime = [
    [0, 'Pilih'], 
    [1, 'Hari ini'],
    [2, 'Pekan ini'],
    [3, 'Bulan ini'],
    [4, 'Kustom']
];

/* End Action Area */
if (!isset($_GET['reportView'])):
?>
    <div class="per_title">
        <h2><?= __('Visitor Web Counter'); ?></h2>
    </div>
    <div class="sub_section">
        <form method="get" action="<?= $_SERVER['PHP_SELF'] . '?' . httpQuery(); ?>" target="reportView">
            <!-- hidden -->
            <input type="hidden" name="mod" value="<?= str_replace(['"', '\'', '`', '<','>'], '', $_GET['mod']) ?>"/>
            <input type="hidden" name="id" value="<?= str_replace(['"', '\'', '`', '<','>'], '', $_GET['id']) ?>"/>
            <!-- end hidden -->
            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0">Tipe Laporan</legend>
                    <?= simbio_form_element::selectList('reportType', $reportType, '','class="form-control col-2"'); ?>
                </div>
            </fieldset>
            <fieldset class="form-group page d-none">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0">Jenis Halaman</legend>
                    <?= simbio_form_element::selectList('reportPage', $reportPage, '','class="form-control col-2"'); ?>
                </div>
            </fieldset>
            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0">Waktu</legend>
                    <?= simbio_form_element::selectList('reportTime', $OptTime, '','class="form-control col-2"'); ?>
                </div>
            </fieldset>
            <fieldset class="form-group date-custom d-none">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0">Date</legend>
                    <div class="col-sm-10 col-md-6">
                        <div class="row">
                            <div class="col">
                                <input type="date" name="start_date" class="form-control" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col">
                                <input type="date" name="end_date" class="form-control" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <input type="hidden" name="reportView" value="true"/>
            <div class="form-group row">
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </form>
    </div>
    <div class="paging-area border-bottom">
        <div class="p-3" id="pagingBox"></div>
    </div>
    <iframe name="reportView" id="reportView" src="<?= $_SERVER['PHP_SELF'] . '?' . httpQuery(['reportView' => 'true']); ?>" frameborder="0" style="width: 100%; height: 500px;"></iframe>
    <script>
        $('#reportTime').change(function(){ if ($(this).val() == 4){ $('.date-custom').removeClass('d-none') } else { $('.date-custom').addClass('d-none');$('input[type="date"]').val('')}})
        $('#reportType').change(function(){if ($(this).val() == 'perPage'){$('.page').removeClass('d-none')} else {$('.page').addClass('d-none');$('#reportPage').val(0)}})
    </script>
<?php
elseif (isset($_GET['reportType']) && !empty($_GET['reportType'])):
    ob_start();
    // Data grid
    $Grid = new VisitorCouterWebReport($dbs = \SLiMS\DB::getInstance('mysqli'));
    $Grid->concatCriteria = false;
    $Grid->table_attr = 'class="s-table table table-sm table-bordered"';


    if ($_GET['reportType'] == 'perPage')
    {
        $_GET['reportType'] = 'perPage:' . $_GET['reportPage'];
    }

    $Column = [
        'accessPage' => [
            'activity' => 'Aktivitas', 
            'count(id)' => 'Jumlah Akses',
            'inputdate' => 'Tanggal Akses'
        ],
        'perPage:page' => [
            'input' => 'Halaman',
            'querystring' => 'Data yang dicari',
            'inputdate' => 'Tanggal Akses'
        ],
        'perPage:search' => [
            'activity' => 'Halaman',
            'querystring' => 'Data yang dicari',
            'inputdate' => 'Tanggal Akses'
        ],
        'perPage:Advance' => [
            'activity' => 'Halaman',
            'querystring' => 'Data yang dicari',
            'inputdate' => 'Tanggal Akses'
        ],
        'perPage:other' => [
            'activity' => 'Halaman',
            'querystring' => 'Data yang dicari',
            'inputdate' => 'Tanggal Akses'
        ]
    ];

    $ColumnSpec = $Column['accessPage'];
    if (isset($Column[$_GET['reportType']]))
    {
        $ColumnSpec = $Column[$_GET['reportType']];
    }

    $timeCriteria = '';
    if (isset($_GET['reportTime']) && !empty($_GET['reportTime']) && $_GET['reportTime'] != 4)
    {
        $criteriaMap = [
            1 => ' and substring(inputdate, 1,10) = \'' . date('Y-m-d') . '\'',
            2 => ' and (substring(inputdate, 1,10) >= \'' . date('Y-m-d', strtotime('-7 day')) . '\' and substring(inputdate, 1,10) <= \'' . date('Y-m-d') . '\')',
            3 => ' and (substring(inputdate, 1,10) >= \'' . date('Y-m-d', strtotime('-30 day')) . '\' and substring(inputdate, 1,10) <= \'' . date('Y-m-d') . '\')'
        ];

        if (isset($criteriaMap[$_GET['reportTime']]))
        {
            $timeCriteria = $criteriaMap[$_GET['reportTime']];
        }
    }

    if ((isset($_GET['start_date']) && isset($_GET['end_date'])) && (!empty($_GET['start_date']) && !empty($_GET['end_date'])))
    {
        $startDate = $dbs->escape_string($_GET['start_date']);
        $endDate = $dbs->escape_string($_GET['end_date']);
        $timeCriteria = ' and (substring(inputdate, 1,10) >= \'' . $startDate . '\' and substring(inputdate, 1,10) <= \'' . $endDate . '\')';
    }

    $Grid
        ->setColumn($ColumnSpec, true)
        ->setTableSpec('vistor_log')
        ->setCriteria('activity is not null' . $timeCriteria);

    $groupBy = 'activity';
    $orderBy = 'count(id) desc';

    function extractJson($dbs, $data)
    {
        if (!is_null($data[1]) && ($Data = json_decode($data[1], true)))
        {
            $HTML = '';
            foreach ($Data ?? [] as $key => $value) {
                if (is_string($value))
                {
                    $HTML .= <<<HTML
                        <div class="d-block"><b>{$key}</b> : <label>{$value}</label></div>
                        HTML;
                }
            }

            return $HTML;
        }

        return '';
    }

    function textToLink($db, $data)
    {
        if (in_array($data[0], ['search','page', 'Advance']))
        {
            $_GET['reportType'] = 'perPage';
            $_GET['reportPage'] = $data[0];
            return '<a href="'. $_SERVER['PHP_SELF'] . '?' . httpQuery() .'">'.$data[0].'</a>';
        }

        return $data[0];
    }

    function modifyPage($dbs, $data)
    {
        return ucwords(str_replace(['_'], ' ', $data[0]));
    }

    if (isset($_GET['reportType']))
    {
        switch ($_GET['reportType']) {
            case 'perPage:search':
            case 'perPage:Advance':
            case 'perPage:other':
            case 'perPage:page':
                $groupBy = '';
                $orderBy = 'activity asc, inputdate desc';
                $Grid->modifyColumnContent(0, 'callback{modifyPage}');
                $Grid->modifyColumnContent(1, 'callback{extractJson}');
                if ($_GET['reportPage'] != 'other') 
                {
                    $Grid->setCriteria('activity = \'' . $dbs->escape_string($_GET['reportPage']) . '\'' . $timeCriteria);
                }
                else
                {
                    $Grid->setCriteria('activity not in (\'search\',\'page\', \'Advance\')' . $timeCriteria);
                }
                break;
            
            default:
                $Grid->modifyColumnContent(0, 'callback{textToLink}');
                break;
        }
    }

    $Grid->show_spreadsheet_export = true;
    $Grid->spreadsheet_export_btn = '<a href="' . AWB . 'modules/reporting/xlsoutput.php" class="s-btn btn btn-default">'.__('Export to spreadsheet format').'</a>';

    $Grid
        ->groupBy($groupBy)
        ->orderBy($orderBy)
        ->create()
        ->createXls('report.xlsx');

    echo $Grid->result();
    // echo $Grid->getSQL(); // debug

    echo '<script type="text/javascript">' . "\n";
    echo 'parent.$(\'#pagingBox\').html(\'' . str_replace(array("\n", "\r", "\t"), '', $Grid->paging_set) . '\');' . "\n";
    echo '</script>';

    $content = ob_get_clean();

    require SB . '/admin/' . $sysconf['admin_template']['dir'] . '/printed_page_tpl.php';
endif;
?>
