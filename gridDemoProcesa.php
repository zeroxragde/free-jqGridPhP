<?php
 error_reporting(E_ALL); 
ini_set("display_errors", 1); 
include("lib/class.jqGrid.php"); 
require_once 'lib/conexion.php';


$eControl= new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);//se crea el objeto de conexion
$eControl->query("SET NAMES utf8"); 




$grid=new jqGridPersistente($eControl);
$sql="SELECT id,
nombre,
edad,
trabajo
FROM personas ";
$grid->setData($sql);

$grid->setColModel(array(0=>array('name'=>"ID","label"=>"MyID")));

$grid->setGridOptions(array('caption'=>"Usuarios",
                            'rowNum'=>7,
							'width'=>900,
							'autowidth'=>false,
							'multiselect'=>false,
							'shrinkToFit'=>false));
							
							

/*		
EVENTOS				
$grid->OnLoadEvent("alert('hola');");
$grid->OnSelectAllEvent("alert('hola');");
$grid->OnSelectRowEvent("alert('hola');");*/

$grid->setColProperty('id', array("label"=>"ID USUARIO", "hidden"=>false,"align"=>'center',"editable"=>true,"width"=>80,"formatter"=>"number"));
$grid->setSelect('trabajo',"SELECT 0,'Sin Trabajo' UNION SELECT id,Descripcion FROM trabajos","Todos");
//$grid->setSelect('n00100claveempresa',array(1=>"Picus"),"Todos");
$grid->setColProperty('trabajo', array("label"=>"Trabajo", "hidden"=>false,"align"=>'center',"editable"=>true,"width"=>480));
$grid->setColProperty('nombre', array("label"=>"Nombre", "hidden"=>false,"align"=>'center',"editable"=>true,"width"=>180));
$grid->setColProperty('edad', array("label"=>"Edad", "hidden"=>false,"align"=>'center',"editable"=>true,"width"=>80));

$grid->setPrimaryKey("id");

$fun=<<<fun
function (val, prop, row) {
	
	return "Hola";
	
}
fun;

$grid->addCol(array("sortable"=>false,
                     "width"=>120,
					 "editable"=>false,
					 "align"=>'center',
					 'name'=>"OPTIONS",
					 'search'=>false, 
					 "label"=>"Notificacion",
					 "formatter"=>"formato"),1,"OPCIONES");


$js=<<<JS
function formato(i,v){
	return "hola";
}
JS;
$grid->setJS($js);


$grid->setSubGrid("gridDemoPrecesaSub.php",array('Nombre'));


$grid->setNavOptions('navigator', array("columns"=>true,
                                         "excel"=>true,
										 "add"=>true,
										 "edit"=>true,
										 "del"=>true,
										 "search"=>true,
										 "refresh"=>false));




$grid->renderGrid("grid","pager",'mygrid');

?>