<?php
 error_reporting(E_ALL); 
ini_set("display_errors", 1); 
include("lib/class.jqGrid.php"); 
require_once 'lib/conexion.php';


$eControl= new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);//se crea el objeto de conexion
$eControl->query("SET NAMES utf8"); 

if (isset($_POST["rowid"])) $rowid=$_POST["rowid"]; else $rowid=""; 
if (isset($_POST["pager"])) $pager=$_POST["pager"]; else $pager=""; 
if (isset($_POST["grid"])) $mygrid=$_POST["grid"]; else $mygrid=""; 
if (isset($_POST["contenedor"])) $contenedor=$_POST["contenedor"]; else $contenedor=""; 

if($rowid==""){
	echo "Error";
	exit();
}
$subgrid=new jqGridPersistente($eControl);
$sql="SELECT id,
imagenurl,
tipo,
urlgo
FROM personas WHERE id=$rowid";
$subgrid->setData($sql);

$subgrid->setPrimaryKey("id");
$subgrid->setGridOptions(array('caption'=>"",
                            'rowNum'=>7,
							'width'=>550,
							'autowidth'=>false,
							'multiselect'=>false,
							'shrinkToFit'=>true));



$subgrid->setColProperty('id', array("label"=>"ID USUARIO", "hidden"=>true));
$subgrid->setColProperty('imagenurl', array("label"=>"Imagen", "hidden"=>false,"align"=>'center',"editable"=>true,"width"=>480));
$subgrid->setColProperty('tipo', array("label"=>"Tipo", "hidden"=>false,"align"=>'center',"editable"=>true,"width"=>480));
$subgrid->setColProperty('urlgo', array("label"=>"URL", "hidden"=>false,"align"=>'center',"editable"=>true,"width"=>480));

$subgrid->notPager(true);



$id=$mygrid."_t";
$mypager=$pager."_t";

$subgrid->renderGrid($id,$mypager,$contenedor);

?>