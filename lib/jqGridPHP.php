<?php
 error_reporting(E_ALL); 
ini_set("display_errors", 1); 
include("class.jqGrid.php");
include("class.better_mysqli.php");

 //$eControl= $_SESSION["CONN"];//se crea el objeto de conexionunset
 //unset($_SESSION["CONN"]);
//$_SESSION["CONN"]->query("SET NAMES utf8"); 

$jqCryp=new jqGridPersistente();

$post_data = file_get_contents('php://input');
$postArr=json_decode($post_data,true);


if(empty($postArr)){
	echo json_encode(array("estado"=>false,"msg"=>"Parametros incompletos"));
}

if (isset($postArr["a"])) $action=$postArr["a"]; else $action="";
if (isset($postArr["c"])) $cols=$postArr["c"]; else $cols="";
if (isset($postArr["codex"])) $codex=$postArr["codex"]; else $codex="";
if (isset($postArr["data"])) $data=$postArr["data"]; else $data="";
if (isset($postArr["mt"])) $dbs=$postArr["mt"]; else $dbs="";
if (isset($postArr["mk"])) $primaryKey=$postArr["mk"]; else $primaryKey="";

if($codex=="" || $cols=="" || $action=="" || $data==""){
	echo json_encode(array("estado"=>false,"msg"=>"Parametros incompletos")); exit();
}




$arr_["debug"]="";
$arr_["estado"]=false;
$arr_["msg"]="Error...";

$llave=date("Ym").md5("edgarCarrizales");
//obtener datos de conexion
$ConfigDB=explode("|",$jqCryp->decrypt($codex,$llave));
$dbs=$jqCryp->decrypt($dbs,$llave);
$primaryKey=$jqCryp->decrypt($primaryKey,$llave);


if($action!="del"){
	//obtener columnas
	$desEncriptar=$jqCryp->decrypt($cols,$llave);
	$desCodificar=base64_decode($desEncriptar);
	$deCompactar=gzinflate($desCodificar);
	$getCols=$deCompactar;
	$arrCols=explode(",",$getCols);
	$arrFinalCols=array();
	foreach($arrCols as $col){
		$colInArr=explode(":",$col);
		$arrFinalCols[$colInArr[0]]=$colInArr[1];
	}


	//DATOS A GUARDAR
	$dataArr = json_decode(json_encode($data),true);
}

// $mysqli = new better_mysqli('your_server', 'your_user', 'your_pass', 'your_db_name');
$eControl= new better_mysqli($ConfigDB[0],$ConfigDB[1],$ConfigDB[2],$ConfigDB[3],$ConfigDB[4]);//se crea el objeto de conexion
$eControl->query("SET NAMES utf8"); 


//Actions
if($action=="edit"){

$sqlUpdate="UPDATE $dbs SET ";
$sqlCampos="";
$idFinal="";
$sqlValores=array();
$sqlTipoValor=array();
//Sacar campo que se utilizara
foreach($dataArr as $userCol=>$newVal){
	if($userCol!="grid_id"){
		if($sqlCampos==""){
			$sqlCampos=$arrFinalCols[$userCol]."=?";
			$sqlValores[]=$newVal;
			
		}else{
			$sqlCampos.=",".$arrFinalCols[$userCol]."=?";
			$sqlValores[]=$newVal;
		}
	}else{
		
		$idFinal=$newVal;
	}
}

$myprimkey=$arrFinalCols[$primaryKey];
$sqlValores[]=$idFinal;//agregamos al final la llave primaria

if($idFinal==""){echo json_encode($arr_);exit();}

$sqlUpdate.=$sqlCampos." WHERE $myprimkey = ?";//'$idFinal'



$eControl->update($sqlUpdate, $sqlValores);	
	
	
	
}
if($action=="add"){
	
	$arr_["estado"]=false;
	$arr_["msg"]="Error...";
	
	
	
	
}
echo json_encode($arr_);




?>