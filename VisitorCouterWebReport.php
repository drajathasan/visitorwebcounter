<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-11-01 09:36:55
 * @modify date 2021-11-01 09:36:55
 * @desc [description]
 */

class VisitorCouterWebReport extends report_datagrid
{
    private string $tableSpec;
    private string $tableColumn;
    private string $Datagrid;
    private string $HeaderInfo = '';
    private object $dbs;
    private string $orderBy;
    private string $groupBy;
    private string $criteria = '';
    public bool $concatCriteria = true;

    public function __construct(mysqli $dbs)
    {
        parent::__construct();
        $this->dbs = $dbs;
    }

    public function setTableSpec(string $Table, array $Join = [])
    {
        // set table 
        $this->tableSpec = $Table . ' ';

        foreach ($Join as $attr) {
            $this->tableSpec .= ' ' . $attr[0] . ' JOIN ' . $attr[1] . ' ON ' . $attr[2];
        }

        return $this;
    }

    public function setColumn($Columns, $withAJAX = false)
    {
        $tableColumn = [];
        foreach ($Columns as $Column => $Label) {
            $tableColumn[] = $Column . " AS '$Label'";
        }

        if (!$withAJAX)
        {
            $this->tableColumn = implode(', ', $tableColumn);
            $this->setSQLColumn($this->tableColumn);
        }
        else
        {
            $this->tableColumn = implode(', ', $tableColumn);
            call_user_func_array([$this,'setSQLColumn'], $tableColumn);
        }

        return $this;
    }

    public function setCriteria(string $criteria)
    {
        if ($this->concatCriteria)
        {
            $this->criteria .= $criteria;
        }
        else
        {
            $this->criteria = $criteria;
        }

        return $this;
    }

    public function getSQL()
    {
        return 'SELECT ' . $this->tableColumn . ' FROM ' . $this->tableSpec . ' ' . $this->sql_criteria . $this->sql_group_by;;
    }

    public function orderBy(string $OrderBy = '')
    {
        $this->orderBy = $OrderBy;
        return $this;
    }

    public function groupBy($GroupBy = '')
    {
        $this->groupBy = $GroupBy;
        return $this;
    }


    public function create(int $numPerPage = 20)
    {
        if (!empty($this->orderBy)) $this->setSQLorder($this->orderBy);
        if (!empty($this->groupBy)) $this->sql_group_by = $this->groupBy;
        if (!empty($this->criteria)) $this->setSQLCriteria($this->criteria);

        $this->Datagrid = $this->createDataGrid($this->dbs, $this->tableSpec, $numPerPage);
        return $this;
    }

    public function createXls(string $FileName, array $Header = [])
    {
        // reset data
        unset($_SESSION['xlsdata']);

        // set query
        $_SESSION['xlsquery'] = 'SELECT ' . $this->tableColumn . ' FROM ' . $this->tableSpec . ' ' . $this->sql_criteria . $this->sql_group_by;

        // Header
        if (count($Header))
        {
            $_SESSION['xlsheader'] = $Header;
        }

        // File name
        $_SESSION['tblout'] = $FileName;

        return $this;

    }

    public function createHeaderInfo($Data)
    {
        $HTML = '<dl class="row">';
        foreach ($Data as $Label => $Value) {
            $HTML .= <<<HTML
                <dt class="col-sm-2">{$Label}</dt>
                <dd class="col-sm-10">{$Value}</dd>
            HTML;
        }
        $HTML .= '</dl>';

        $this->HeaderInfo = $HTML;

        return $this;
    }

    public function result()
    {
        return $this->HeaderInfo . $this->Datagrid;
    }
}
