<?php
ini_set("display_errors",1);
$aColumns = array('register_id','student_name','phone_no','school_name','select_course','done_on');
/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "register_id";

/* DB table to use */

/* Database connection information */
session_start();
$path='../../../application/models/Database.php';
$path1='../../../application/models/Util.php';
require_once $path;
require_once $path1;
$db=new Application_Model_Database();
$util=new Application_Model_Util();

$sTable="register_tb";


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to edit below this line
 */



/*
 * Paging
 */
$sLimit = "url_id";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
    $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
        intval( $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
$sOrder = "";
if ( isset( $_GET['iSortCol_0'] ) )
{
    $sOrder = "ORDER BY  ";
    for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
    {
        if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
        {
            $sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
                ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
        }
    }

    $sOrder = substr_replace( $sOrder, "", -2 );
    if ( $sOrder == "ORDER BY" )
    {
        $sOrder = "";
    }
}


/*
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$sWhere = "";
if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
{
    $sWhere = "WHERE (";
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
        {
            $sWhere .= "`".$aColumns[$i]."` LIKE '%".addslashes( $_GET['sSearch'] )."%' OR ";
        }
    }
    $sWhere = substr_replace( $sWhere, "", -3 );
    $sWhere .= ')';
}

/* Individual column filtering */
for ( $i=0 ; $i<count($aColumns) ; $i++ )
{
    if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
    {
        if ( $sWhere == "" )
        {
            $sWhere = "WHERE ";
        }
        else
        {
            $sWhere .= " AND ";
        }
        $sWhere .= "`".$aColumns[$i]."` LIKE '%".addslashes($_GET['sSearch_'.$i])."%' ";
    }
}


/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
		,{$sIndexColumn} FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
		";
$rResult = $db->query( $sQuery);

/* Data set length after filtering */
$sQuery = "
		SELECT FOUND_ROWS()
	";
$rResultFilterTotal = $db->query( $sQuery);
$aResultFilterTotal = $db->fetch($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
$rResultTotal = $db->query( $sQuery);
$aResultTotal = $db->fetch($rResultTotal);
$iTotal = $aResultTotal[0];
//echo $sQuery;
/*
 * Output
 */
$output = array(
    "sEcho" => intval($_GET['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

while ( $aRow = $db->fetch( $rResult ) )
{
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        /*if ( $aColumns[$i] == "url_id" )
        {
           // $str1='<a ';if(isset($_SESSION['admin_page_view'])) $str1.='href="inventory_admin_role.php?id='.$aRow['role_id'].'"'; $str1.='>'.$aRow['role_name'].'</a>';
             $str1='<a href="'.$aRow['url'].'">'.$aRow['url_id'].'</a>';

            $row[]=$str1;
        }
        elseif ( $aColumns[$i] == "url" )
        {
           // $str1='<a ';if(isset($_SESSION['admin_page_view'])) $str1.='href="inventory_admin_role.php?id='.$aRow['role_id'].'"'; $str1.='>'.$aRow['role_name'].'</a>';
             $str1='<a href="'.$aRow['url'].'">'.$aRow['url'].'</a>';

            $row[]=$str1;
        }
        elseif ( $aColumns[$i] == "user_id" )
        {
             $str1='<form method="post">';
             $str1.='<input type="hidden" name="url_id" value="'.$aRow['url_id'].'"><button class="btn btn-danger btn-sm" name="delete_url" onclick="return confirm(\'Are you sure?\');">Delete</button>';
             $str1.='</form>';
            $row[]=$str1;
        }
        else*/ if ( $aColumns[$i] != ' ' )
    {
        /* General output */
        $row[] = $aRow[ $aColumns[$i] ];
    }
    }
    $row[]='';
    $output['aaData'][] = $row;
}
echo json_encode( $output );
