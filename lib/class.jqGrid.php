<?php
class jqGridPersistente{
	private $conn="";
	private $query="";
	private $data=array();
	private $error="";
	private $gridOptions=array();
	private $colModel=array();
	private $onload="";
	private $onSelectAll="";
	private $onSelectRow="";
	private $ColPropiedades=array();
	private $selectData=array();
	private $jsAdd="";
	private $colsAdd=array();
	private $FormatosPredifinidos=array("integer","number","currency","date","email","link","showlink","checkbox","select","actions");
	private $navBtnCustomDefault=array("storeClear","cols","refresh");
	private $subGrid=array();
	private $mkey="";
	private $navOptions=array();
	private $buscarAlDarEnter='false';
	private $noPager=false;
	private $arrNoValidas=array("onSelectAll","loadComplete","onSelectRow","sortable","autoResizing","datatype","colModel");
    private $arrModificadores=array("rowNum","page",'search','postData','sortname','sortorder');
	private $dbs="";
	private $arrUserDBInfo=array();
	private $llave="";
	private $userButtons=array();
	
    function __construct() {

		$a=array();
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {
			if(sizeof($a)>1){
				 call_user_func_array(array($this,$f),array($a));
			}else{
				 call_user_func_array(array($this,$f),$a);
			}
           
        }
   }
   
   
	private function __construct0(){
		   
	}
	private function __construct1($c){
		   $this->conn=$c;
	}

	private function __construct5($c){

		$this->arrUserDBInfo=$c;
		$this->conn=new mysqli($c[0],$c[1],$c[2],$c[3],$c[4]);
	}

	
	
	
 
 
	
	private static function generarLlave($texto){
		$abc=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$ABC=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$simbolo=array('!','@','#','$','%','^','&','*','(',')','_','+','=','<','>','?','/');
		$numero=array(1,2,3,4,5,6,7,8,9,0);
		$abc=array_reverse($abc);

		$llave=$texto;
		$key="";
		
      for($k=0;$k<strlen($llave);$k++){
		  
              $keychar = substr($llave,$k,1);
              $val=0;
			  
              for($x=0;$x<sizeof($abc);$x++){
                 if($abc[$x]==$keychar){
                    $val=$x;
                 }
       		    }
				
				
				if($val==0){
                  for($x=0;$x<sizeof($ABC);$x++){
                     if($ABC[$x]==$keychar){
                        $val=$x;
                     }
                   }
                }
                
				if($val==0){
                  for($x=0;$x<sizeof($simbolo);$x++){
                     if($simbolo[$x]==$keychar){
                        $val=$x;
                     }
                   }
                }
                
			   if($val==0){
                  for($x=0;$x<sizeof($numero);$x++){
                     if($numero[$x]==$keychar){
                        $val=$x;
                     }
                   }
                }
               $key=(int)$key+(int)$val;
              
      }
	  
	return $key;
	}
 
	private static function ByteToString($bytes){
		
		//$byte_array = call_user_func_array('pack', array_merge(array('C*'), $bytes )); PHP 6<
		
		$cadena_final="";
		foreach($bytes as $byte){
			$cadena_final.=pack('C*', $byte);
		}

		return $cadena_final;
	}
	
	private static function StringToByte($string){
	   return unpack('C*', $string);
	}
		
	private function js_str($s){
		return '"' . addcslashes($s, "\0..\37\"\\") . '"';
	}

	private function js_array($array){
		$temp = array_map('js_str', $array);
		return '[' . implode(',', $temp) . ']';
	}

	private function array_extend($a, $b) {
		foreach($b as $k=>$v) {
			if( is_array($v) ) {
				if( !isset($a[$k]) ) {
					$a[$k] = $v;
				} else {
					$a[$k] = $this->array_extend($a[$k], $v);
				}
			} else {
				$a[$k] = $v;
			}
		}
		return $a;
	}
	
	private function showError(){
		echo $this->error;
	}
	
	private function str_replace_first($search, $replace, $subject) {
     $pos = strpos($subject, $search);
		if ($pos !== false) {
			return substr_replace($subject, $replace, $pos, strlen($search));
		}
      return $subject;
    }
	
	
		
	public function encrypt($cadena,$key){
		

		$llave=$this->generarLlave($key);




		$arr_bytes=$this->StringToByte($cadena);


		$sizeArr=sizeof($arr_bytes);

		if($sizeArr>2){
			
		 $indexKey=round($sizeArr/2);

		}else{
			  $indexKey=1;
		}


		$lastIndex=$sizeArr;
		$initIndex=1;


		if($initIndex!=$indexKey){

			$arr_bytes[$initIndex]=$arr_bytes[$initIndex]+$llave;
			$arr_bytes[$lastIndex]=$arr_bytes[$lastIndex]+$llave;
			$arr_bytes[$indexKey]=$arr_bytes[$indexKey]+$llave;

		}else{

			$arr_bytes[$initIndex]=$arr_bytes[$initIndex]+$llave;
			$arr_bytes[$lastIndex]=$arr_bytes[$lastIndex]+$llave;
			

		}

		return base64_encode($this->ByteToString($arr_bytes));	
			
		
	}

	public function decrypt($cadena,$key){
			
		$cadena=base64_decode($cadena);
		$llave=$this->generarLlave($key);




		$arr_bytes=$this->StringToByte($cadena);


		$sizeArr=sizeof($arr_bytes);

		if($sizeArr>2){
			
		 $indexKey=round($sizeArr/2);

		}else{
			  $indexKey=1;
		}


		$lastIndex=$sizeArr;
		$initIndex=1;


		if($initIndex!=$indexKey){

			$arr_bytes[$initIndex]=$arr_bytes[$initIndex]-$llave;
			$arr_bytes[$lastIndex]=$arr_bytes[$lastIndex]-$llave;
			$arr_bytes[$indexKey]=$arr_bytes[$indexKey]-$llave;

		}else{

			$arr_bytes[$initIndex]=$arr_bytes[$initIndex]-$llave;
			$arr_bytes[$lastIndex]=$arr_bytes[$lastIndex]-$llave;
			

		}

		return $this->ByteToString($arr_bytes);	
			
	}
	
	
	public function setSearchOnEnter($t){
		$this->buscarAlDarEnter="'".$t."'";
	}
	public function setJS($j){
		$this->jsAdd=$j;
	}
	public function setData($d){
		$this->query=$d;
	}
	public function setGridOptions($arr){
		$this->gridOptions=$arr;
	}
	public function setColProperty($NameCol,$prop){
		$this->ColPropiedades[$NameCol]=$prop;
	}
	public function setColModel($arr){
		$this->colModel=$arr;
	}
	public function OnLoadEvent($js){
		$this->onload=$js;
	}
	public function OnSelectAllEvent($js){
		$this->onSelectAll=$js;
	}
	public function OnSelectRowEvent($js){
		$this->onSelectRow=$js;
	}
	public function setPrimaryKey($colName){
		$this->mkey=$colName;
	}
	public function setSelect($colName,$arrOrSql,$textAll){
		if(!$this->conn){
			$this->error='Se debe asignar una conexion en el contructor<br/>';	$this->showError();return false;
		}
		if(is_array($arrOrSql)){
			$this->selectData[$colName]=$arrOrSql;
			$this->selectData[$colName]["textAll"]=$textAll;
		}else{
			
			$result=mysqli_query($this->conn,$arrOrSql);
			
			$info_tabla=$result->fetch_fields();
			$x=0;
			
			foreach($info_tabla as $tabla){$x++;}

			if($x!=2){return false;}
			if(!$result){return false;}
			$arrTmp=array();
		    
				 while($row=mysqli_fetch_array($result,MYSQLI_NUM)){
					 $this->selectData[$colName][$row[0]]=$row[1];
					 $this->selectData[$colName]["textAll"]=$textAll;
				 }
			
		}
	   
	}
	public function addCol($prop,$pos,$NewCol){
		$index=sizeof($this->colsAdd)+1;
		$this->colsAdd[$index]["Posicion"]=$pos;
		$this->colsAdd[$index]["Propiedades"]=$prop;
		$this->colsAdd[$index]["Nombre"]=$NewCol;
	}
	public function setSubGrid($url,$postCols=""){
		$this->subGrid["URL"]=$url;
		$this->subGrid["Cols"]=$postCols;
	}
	public function setNavOptions($action,$prop){
		$this->navOptions[$action]=$prop;
	}
	public function notPager($e){
		$this->noPager=$e;
	}
	public function setDBs($d){
		$this->dbs=$d;
	}
	public function setButton($b){
		
		$this->userButtons[]=$b;
		
	}
	

	
public function renderGrid($mygridName,$pagerName, $divContenedora){
	
$this->llave=date("Ym").md5("edgarCarrizales");
		
if(!$this->conn){
	$this->error='Se debe asignar una conexion en el contructor<br/>';	$this->showError();return false;
}	
if($this->mkey==""){
	$this->error='Se debe asignar una llave primaria<br/>';	$this->showError();return false;
}
	
if($this->dbs==""){
	$this->error='Se debe especificar una bases de datos a afectar $obj->setDBs("base")<br/>';	$this->showError();return false;
}
$arrColNames=array();
if(empty($this->conn)){
	$this->error='Se debe especificar una consulta $obj->setData("consulta")<br/>';	$this->showError();return false;
}else{
		$result=mysqli_query($this->conn,$this->query);
		if($result){ 
				$info_tabla=$result->fetch_fields();
				$x=0;
				//print_r($info_tabla);
                 foreach($info_tabla as $tabla){
					 $arrColNames[$x]["Original"]=$tabla->orgname;
					 $arrColNames[$x]["Asignado"]=$tabla->name;
					 $x++;
					
				 }
					
		}else{
			$this->error='Error al intentar crear el ColModel: '.mysqli_error($this->conn)."<br/>";	$this->showError();return false;
		}

	
	if(empty($this->colModel)){
		$arrTMPColModel=array();
				$x=0;
		foreach($arrColNames as $ColName){
			$nombre=$ColName["Asignado"];
			$arrTMPColModel[$x]['name']=$nombre;
			if(isset($this->ColPropiedades[$nombre])){
				
				foreach($this->ColPropiedades[$nombre] as $nombreProp=>$valor){
					if($nombreProp!="name"){
						$arrTMPColModel[$x][$nombreProp]=$valor;
					}
				}
			}
			$x++;
		}

	}else{
		$arrTMPColModel=array();
		$x=0;
		foreach($arrColNames as $ColName){
			
			$nombre=$ColName["Asignado"];
			$arrTMPColModel[$x]['name']=$nombre;
			if(isset($this->ColPropiedades[$nombre])){
				
				foreach($this->ColPropiedades[$nombre] as $nombreProp=>$valor){
					if($nombreProp!="name"){
						$arrTMPColModel[$x][$nombreProp]=$valor;
					}
				}
			}
			$x++;
		}

	}
		
		
		
        $myColModel=$this->array_extend($this->colModel,$arrTMPColModel);
		
		if(!empty($this->colsAdd)){
			
			foreach($this->colsAdd as $nuevaCol){
	            $nombre=$nuevaCol["Nombre"];
				$opciones=$nuevaCol["Propiedades"];
			    $opciones["name"]=$nombre;
				$opciones["formatter"]=$nuevaCol["Propiedades"]["formatter"];
				$arr[]=$opciones;
						
				array_splice( $myColModel, $nuevaCol["Posicion"], 0, $arr ); // splice in at position 3
			}
			
		}
		

		
		$colModelJSON="[";
		$cantidaCols=sizeof($myColModel)-1;
		//
		//print_r($myColModel);
		
		foreach($myColModel as $index=>$Col){ 
		   
		   if($Col["name"]==$this->mkey){
			   $myColModel[$index]["key"]=true;
		   }
		}
		
		foreach($myColModel as $index=>$Col){ 
		   
		   
		   $colModelJSON.="{";
		   $propiedades="";
			foreach($Col as $campo=>$data){
				$valor="";
				    
					if(strpos($campo,"formatter")!==false || strpos($campo,"unformat")!==false){
						if(in_array($data,$this->FormatosPredifinidos)){
							$valor='"'.$data.'"';
						}else{
							$valor=$data;
						}
					}else{
						 $valor='"'.$data.'"';
					}
				
				if($data==""){
					$valor='false';
				}
				if($data=="true"){
					$valor='true';
				}
				 if($propiedades==""){
					$propiedades="'$campo'".":".$valor;
				 }else{
					 $propiedades.=",\r\n'$campo'".":".$valor;
				 }
				 
				
			}
			$colModelJSON.=$propiedades;
			
			if($cantidaCols==$index){
				$colModelJSON.="}";
			}else{
				$colModelJSON.="},";
			}
		   
			
		}
		$colModelJSON.="]";
		
		
       $this->colModel=$colModelJSON;
		
	$arrData=array();
	$test=mysqli_query($this->conn,$this->query);
  if($test){
	  $x=0;
	  while($row=mysqli_fetch_array($test,MYSQLI_ASSOC)){
		 foreach($myColModel as $colName){
			 if(isset($row[$colName['name']])){
				  $arrData[$x][$colName['name']]=$row[$colName['name']];
			 }else{
				  $arrData[$x][$colName['name']]="";
			 }
		 }
		 $x++;
	  }
  }
	

	
}

$modelo=$this->colModel;
$data=json_encode($arrData);


//Asignar Cols Originales
$arr_Original_Cols=array();
foreach($arrColNames as $col){
	$original=$col["Original"];
	$colModel=$col["Asignado"];
	$arr_Original_Cols[$colModel]=$original;
}
$jsonOriginalCols=json_encode($arr_Original_Cols);
  // var myColsOriginal=$jsonOriginalCols;\r\n
$jsGridData=<<<GRIDDATA
        var mygrid=$('#$mygridName');\r\n
		var myColModel=$modelo;\r\n
		var myData = $data;\r\n
	
GRIDDATA;


//{
$jsTools=<<<H
		initDateSearch = function (elem) {
			$(elem).datepicker({
				dateFormat: "yy-mm-dd",
				autoSize: true,
				changeYear: true,
				changeMonth: true,
				showButtonPanel: true,
				showWeek: true,
				onSelect: function () {
					if (this.id.substr(0, 3) === "gs_") {
						setTimeout(function () {
							$(elem).closest("div.ui-jqgrid-hdiv").next("div.ui-jqgrid-bdiv").find("table.ui-jqgrid-btable").first()[0].triggerToolbar();
						}, 50);
					} else {
						// to refresh the filter
						$(this).trigger("change");
					}
				}
			});
		},
		numberSearchOptions = ["eq", "ne", "lt", "le", "gt", "ge", "nu", "nn", "in", "ni"],
		numberTemplate = {formatter: "number", align: "right", sorttype: "number", searchoptions: { sopt: numberSearchOptions }},
		myDefaultSearch = "cn",
		refreshSerchingToolbar = function (mygrid, myDefaultSearch) {
			var p = mygrid.jqGrid("getGridParam"), postData = p.postData, filters, i, l,
				rules, rule, iCol, cm = p.colModel,
				cmi, control, tagName;

			for (i = 0, l = cm.length; i < l; i++) {
				control = $("#gs_" + $.jgrid.jqID(cm[i].name));
				if (control.length > 0) {
					tagName = control[0].tagName.toUpperCase();
					if (tagName === "SELECT") { // && cmi.stype === "select"
						control.find("option[value='']")
							.attr("selected", "selected");
					} else if (tagName === "INPUT") {
						control.val("");
					}
				}
			}

			if (typeof (postData.filters) === "string" &&
					typeof (mygrid[0].ftoolbar) === "boolean" && mygrid[0].ftoolbar) {

				filters = $.parseJSON(postData.filters);
				if (filters && filters.groupOp === "AND" && filters.groups === undefined) {
					// only in case of advance searching without grouping we import filters in the
					// searching toolbar
					rules = filters.rules;
					for (i = 0, l = rules.length; i < l; i++) {
						rule = rules[i];
						iCol = p.iColByName[rule.field];
						if (iCol >= 0) {
							cmi = cm[iCol];
							control = $("#gs_" + $.jgrid.jqID(cmi.name));
							if (control.length > 0 &&
									(((cmi.searchoptions === undefined ||
									cmi.searchoptions.sopt === undefined)
									&& rule.op === myDefaultSearch) ||
									  (typeof (cmi.searchoptions) === "object" &&
										  $.isArray(cmi.searchoptions.sopt) &&
										  cmi.searchoptions.sopt.length > 0 &&
										  cmi.searchoptions.sopt[0] === rule.op))) {
								tagName = control[0].tagName.toUpperCase();
								if (tagName === "SELECT") { // && cmi.stype === "select"
									control.find("option[value='" + $.jgrid.jqID(rule.data) + "']")
										.attr("selected", "selected");
								} else if (tagName === "INPUT") {
									control.val(rule.data);
								}
							}
						}
					}
				}
			}
		},
		cm = myColModel,
		saveObjectInLocalStorage = function (storageItemName, object) {
			if (window.localStorage !== undefined) {
				window.localStorage.setItem(storageItemName, JSON.stringify(object));
			}
		},
		removeObjectFromLocalStorage = function (storageItemName) {
			if (window.localStorage !== undefined) {
				window.localStorage.removeItem(storageItemName);
			}
		},
		getObjectFromLocalStorage = function (storageItemName) {
			if (window.localStorage !== undefined) {
				return $.parseJSON(window.localStorage.getItem(storageItemName));
			}
		},
		myColumnStateName = function (grid) {
			return window.location.pathname + "#" + grid[0].id;
		},
		idsOfSelectedRows = [],
		getColumnNamesFromColModel = function () {
			var colModel = this.jqGrid("getGridParam", "colModel");
			return $.map(colModel, function (cm, iCol) {
				// we remove "rn", "cb", "subgrid" columns to hold the column information 
				// independent from other jqGrid parameters
				return $.inArray(cm.name, ["rn", "cb", "subgrid"]) >= 0 ? null : cm.name;
			});
		},
		saveColumnState = function () {
			var p = this.jqGrid("getGridParam"), colModel = p.colModel, i, l = colModel.length, colItem, cmName,
				postData = p.postData,
				columnsState = {
					search: p.search,
					page: p.page,
					rowNum: p.rowNum,
					sortname: p.sortname,
					sortorder: p.sortorder,
					cmOrder: getColumnNamesFromColModel.call(this),
					selectedRows: idsOfSelectedRows,
					colStates: {}
				},
				colStates = columnsState.colStates;

			if (postData.filters !== undefined) {
				columnsState.filters = postData.filters;
			}

			for (i = 0; i < l; i++) {
				colItem = colModel[i];
				cmName = colItem.name;
				if (cmName !== "rn" && cmName !== "cb" && cmName !== "subgrid") {
					colStates[cmName] = {
						width: colItem.width,
						hidden: colItem.hidden
					};
				}
			}
			saveObjectInLocalStorage(myColumnStateName(this), columnsState);
		},
		myColumnsState="",
		isColState="",
		restoreColumnState = function (colModel) {
			var colItem, i, l = colModel.length, colStates, cmName,
				columnsState = getObjectFromLocalStorage(myColumnStateName(this));

			if (columnsState) {
				colStates = columnsState.colStates;
				for (i = 0; i < l; i++) {
					colItem = colModel[i];
					cmName = colItem.name;
					if (cmName !== "rn" && cmName !== "cb" && cmName !== "subgrid") {
						colModel[i] = $.extend(true, {}, colModel[i], colStates[cmName]);
					}
				}
			}
			return columnsState;
		},
		updateIdsOfSelectedRows = function (id, isSelected) {
			var index = $.inArray(id, idsOfSelectedRows);
			if (!isSelected && index >= 0) {
				idsOfSelectedRows.splice(index, 1); // remove id from the list
			} else if (index < 0) {
				idsOfSelectedRows.push(id);
			}
		},
		firstLoad = true;

	myColumnsState = restoreColumnState.call(mygrid, cm);
	isColState = myColumnsState !== undefined && myColumnsState !== null;
	idsOfSelectedRows = isColState && myColumnsState.selectedRows !== undefined ? myColumnsState.selectedRows : [];
	



	
	

   var getUniqueNames = function(columnName) {
		var texts = mygrid.jqGrid('getCol',columnName), uniqueTexts = [],
			textsLength = texts.length, text, textsMap = {}, i;
		for (i=0;i<textsLength;i++) {
			text = texts[i];
			if (text !== undefined && textsMap[text] === undefined) {
				// to test whether the texts is unique we place it in the map.
				textsMap[text] = true;
				uniqueTexts.push(text);
			}
		}
		return uniqueTexts;
	},
	buildSearchSelect = function(uniqueNames,textAll) {

			var values=":"+textAll;
			if(Array.isArray(uniqueNames)){
				$.each (uniqueNames, function() {
					values += ";" + this + ":" + this;
				});						
			}else{
				$.each (uniqueNames, function(i,v) {
					values += ";" + i + ":" + v;
				});	
			}

			return values;
	
	
	},
	setSearchSelect = function(columnName,arrayData,textAll) {
		if(arrayData==undefined){
			arrayData=getUniqueNames(columnName);
		}
		mygrid.jqGrid('setColProp', columnName,
					{
						stype: 'select',
						searchoptions: {
							value:buildSearchSelect(arrayData,textAll),
							sopt:['eq']
						}
					}
		);
	};
	
	
	
	
	
	
	
	
H;
//}






				
$rowNum="isColState ? myColumnsState.rowNum : 10";
$page="isColState ? myColumnsState.page : 1";
$search="isColState ? myColumnsState.search : false";
$postData="isColState ? { filters: myColumnsState.filters } : {}";
$sortname="isColState ? myColumnsState.sortname : ''";
$sortorder="isColState ? myColumnsState.sortorder : ''";

$userConfig=array();
 foreach($this->gridOptions as $opcion=>$valor){
	 if(!in_array($opcion,$this->arrNoValidas)){
		 if(in_array($opcion,$this->arrModificadores)){//Si es una opcion que se deba modificar
			 switch($opcion){
				 case "rowNum":
				   $rowNum="isColState ? myColumnsState.$opcion : ".$valor;
				 break;
				 case "page":
				   $page="isColState ? myColumnsState.$opcion : ".$valor;
				 break;
				 case "search":
				   $search="isColState ? myColumnsState.$opcion : ".$valor;
				 break;
				 case "postData":
				   $postData="isColState ? { filters: myColumnsState.filters } : ".$valor;
				 break;
				 case "sortorder":
				   $sortorder="isColState ? myColumnsState.$opcion : '".$valor."'";
				 break;
			 }
		 }else{
			 
			  $userConfig[$opcion]=$valor;
		 }
	 }
	 
 }

$myConfig=json_encode($userConfig,JSON_NUMERIC_CHECK);


$Eonload=$this->onload;
$EonSelectAll=$this->onSelectAll;
$EonSelectRow=$this->onSelectRow;
$colsProp=json_encode($this->ColPropiedades);
$jsAddGrid=$this->jsAdd;
$selctores=json_encode($this->selectData);

//Inicio del subgrid
$subGridAdd="";

if(!empty($this->subGrid)){

$url=$this->subGrid['URL'];
$colsJson="";
if(!empty($this->subGrid['Cols'])){

  $colsSubGrid=", ";
  foreach($this->subGrid['Cols'] as $col){
	  if($colsSubGrid==""){
		  $colsSubGrid="$col : mygrid.jqGrid ('getCell', rowId, '$col')";
	  }else{ 
		   $colsSubGrid=", $col : mygrid.jqGrid ('getCell', rowId, '$col')";
	  }
  }
}

	$subGridAdd=",\r\n subGrid: true,
	             subGridRowExpanded: function (subgridDivId, rowId) {
					 
					 $('#' + subgridDivId).css({margin: 0,
					                            'margin-top': '-25px',
												'margin-bottom': '-25px'}).load('$url',{contenedor:subgridDivId,rowid: rowId,grid:'$mygridName',pager:'$pagerName' $colsSubGrid});
				 }";
	
}
//Fin de subgrid

//Url de la pagina
$myurl=basename($_SERVER['PHP_SELF']);
//Cancelar el pager
$buscarAlEnter=$this->buscarAlDarEnter;
if(!$this->noPager){
	$setPager="'#$pagerName'";
}else{
	$setPager='false';
}



///Botones en el navegador
$arrNav=array();
$jsonNavOpt['edit']=false;
$jsonNavOpt['add']=false;
$jsonNavOpt['del']=false;
$jsonNavOpt['refresh']=false;
$jsonNavOpt['search']=false;
$jsonBtnCustomDefaultT=array();
$optForms=array();
$navOpDefault=array_keys($jsonNavOpt);

if(isset($this->navOptions['navigator'])){
	//{edit: false, add: false, del: false}


	foreach($this->navOptions['navigator'] as $op=>$prop){
		if(in_array($op,$navOpDefault)){
			if($op!="refresh"){
				
				$jsonNavOpt[$op]=$prop;
				$myKey=array_search($op,$navOpDefault);
				
				
				if(isset($this->navOptions[$op])){
					$optForms[$myKey]=$this->navOptions[$op];
					
				}else{
					$optForms[$myKey]="{}";
				}
				
			}else{
			   
					$jsonBtnCustomDefaultT[$op]=$prop;
				
			}
		}else{
			if(in_array($op,$this->navBtnCustomDefault)){
				$jsonBtnCustomDefaultT[$op]=$prop;
			}
		}
		
	}
		
}



$jsonBtnCustomDefault=json_encode($jsonBtnCustomDefaultT);
$jsonNavOpt=json_encode($jsonNavOpt);
$jsonOptForms="";

ksort($optForms);

$jsonOptForms="";
$jsonPropEnd="";
$reflector = new ReflectionClass("jqGridPersistente");
$fn = $reflector->getFileName();
$u1= dirname($fn);
$arrU=explode("/",$u1);




$rutaActual=$arrU[sizeof($arrU)-1];
$reloadGrid="";
$userDB="";
$passDB="";
$hostDB="";
$portDB="";


$dataDBFinal='';
$finalColsDataFull="";
if(!empty($this->arrUserDBInfo)){
	$reloadGrid='$("#$divContenedora").load("$myurl");';
	$dataDB=implode("|",$this->arrUserDBInfo);

	
	$dataDBFinal=$this->encrypt($dataDB,$this->llave);
	
	
	$finalCols="";
	foreach($arr_Original_Cols as $colName=>$orname){
		if($finalCols==""){
			$finalCols=$colName.":".$orname;
		}else{
			$finalCols.=",".$colName.":".$orname;
		}
	}

    $compactar=gzdeflate($finalCols,9);
	$codificar=base64_encode($compactar);
	$finalColsDataFull=$this->encrypt($codificar,$this->llave);//encriptar
	

}

$primKey=$this->encrypt($this->mkey,$this->llave);
$dbs=$this->encrypt($this->dbs,$this->llave);
$functionAdd=<<<F

    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "POST", "$rutaActual/jqGridPHP.php", false ); // false for synchronous request
	xmlHttp.setRequestHeader("Content-Type", "application/json");
	xmlHttp.send(JSON.stringify({c:"$finalColsDataFull", 
	                             codex:"$dataDBFinal",
								 mt:'$dbs',
								 mk:'$primKey',
								 data:postdata,
								 a:{action}
								 }));
  
  
	
	
	var respuesta=JSON.parse(xmlHttp.responseText);
    
	  $("#$divContenedora").load("$myurl");
	
	return [respuesta.estado,respuesta.msg];
	
							
F;

$defaultFunctions=" $functionAdd}";
$fullDefaultFunction="beforeSubmit:function(postdata,form){ $functionAdd }";

$functionAddForm=<<<fa

var selCols=$selctores;


		 
$.each(selCols,function(i,v){
	
var valActual=$("#"+i,formid).val();
var prop='role="select"'+
         'separator=":"'+
		 'delimiter=";"'+
		 'id="'+i+'"'+
		 'name="'+i+'"'+
		 'size="1"'+
		 'class="FormElement"';

var opt="";
$.each(v,function(i,v){

	if(i!="textAll"){
		if(v==valActual){
			opt+='<option selected role="option" value="'+i+'">'+v+'</option>';
		}else{
			opt+='<option role="option" value="'+i+'">'+v+'</option>';
		}		
	}

	
});	 
	$("#"+i,formid).replaceWith("<select "+prop+">"+opt+"</select>");

	
});

fa;
$defaultFunctionsForm=" $functionAddForm}";
$fullDefaultFunctionForm="beforeShowForm:function(formid){ $functionAddForm }";

foreach($optForms as $index=>$data){
	$tmp="";

	if($data != "{}"){
		  // print_r($data);
		$tmp.="{";
		$p=0;
		$campoSubmit=false;
		$campoShowForm=false;
		$cantidadOp=sizeOf($data)-1;
		 foreach($data as $campo=>$valor){
			 if(is_bool($valor)){
				 if($valor==false){
					 $valor='false';
				 }else{
					 $valor='true';
				 }
			 }
			 if(!is_numeric($valor)){
				 if(strpos($valor,"function")===false){
				     
					 $valor="'".$valor."'";
				 }
			 }
			 //Agregamos codigo a las funciones que el user especifico
			 if($campo=="beforeSubmit"){
				  $campoSubmit=true;
			      $valor=$this->str_replace_first("}", $defaultFunctions, $valor); 
				  $valor="\r\n ".str_replace("{action}", "'".$navOpDefault[$index]."'",$valor);
			 }
			 
			 if($campo=="beforeShowForm"){
				  $campoShowForm=true;
			      $valor=$this->str_replace_first("}", $defaultFunctionsForm, $valor); 
			 }
			 

			 
			if($tmp=="{"){
				$tmp.="$campo : $valor";
			}else{
				$tmp.=",\r\n $campo : $valor";
			}
			//Agregamos el campo por si el user no lo especifica
			 if($p==$cantidadOp){
			  if(!$campoSubmit){
				  $campoSubmit=true;
				  $tmp.=",\r\n ".str_replace("{action}", "'".$navOpDefault[$index]."'",$fullDefaultFunction);
				  
			  }
			  if(!$campoShowForm){
				  $campoSubmit=true;
				  $tmp.=",\r\n ".$fullDefaultFunctionForm;
				  
			  }
			 }else{$p++;}

			
		  }
		  
		$tmp.="},";
	}else{
	  $valor=str_replace("{action}", "'".$navOpDefault[$index]."'","\r\n {".$fullDefaultFunction.",\r\n".$fullDefaultFunctionForm."}");
	 
	  $tmp=$valor.",";
	}
    $jsonPropEnd.=$tmp;
}

$jsonPropEnd=substr($jsonPropEnd,0,-1);
$jsonOptForms.=$jsonPropEnd;
$jsonOptForms.="";

//Agregar los custom buttons


//Fin de botones en el navegador


//Eventos
$eventosGrid="";
if(isset($this->navOptions['event'])){
	foreach($this->navOptions['event'] as $nombre=>$funcion){
       if(!in_array($nombre,$this->arrNoValidas)){
		     $eventosGrid.="$nombre : $funcion,";
	   }
	}
}
//Eventos
$jqEvent="";
//jqEvent
if(isset($this->navOptions['jqEvent'])){
	foreach($this->navOptions['jqEvent'] as $nombre=>$funcion){
       if(!in_array($nombre,$this->arrNoValidas)){
		$jqEvent.="mygrid.bind('$nombre',$funcion);\r\n";
	   }
	}
}
//jqEvent

//botones
$botones="''";
if(!empty($this->userButtons)){
	$botones=json_encode($this->userButtons);
	$cantidadBotones=sizeOf($this->userButtons)-1;
	$initBoton="[";
	foreach($this->userButtons as $index=>$boton){
		$initBoton.="{";
		$tmp="";
		foreach($boton as $config=>$val){
			if(strpos($val,"function")===false){
				$val="'".$val."'";
			}
			if($tmp==""){
				$tmp.=$config.":".$val;
			}else{
				$tmp.=", \r\n ".$config.":".$val;
			}
			
		}
		
		$initBoton.=$tmp;
		
		if($cantidadBotones==$index){
			$initBoton.="}";
		}else{
			$initBoton.="},";
		}
		
		
		
	}
	
	$initBoton.="]";
	
	$botones=$initBoton;
}


$initjs=<<<INITJS
<script>
window.mensaje="error";
window.estado=false;
window.control=false;
$(document).ready(function(){
	
	$jsGridData
	
	$jsTools
	
	mygrid.jqGrid('GridUnload');
	var userOpt=$myConfig;
	
	var opciones={
                data: myData,
                rownumbers: true,
                ignoreCase: true,
                iconSet: "fontAwesome",
                shrinkToFit: false,               
                rowList: [5, 10, 20],
                pager: $setPager,
                gridview:true,
                caption: " ",
                height: "auto",	
				rowNum: $rowNum,
                page: $page,
                search: $search,
                postData: $postData,
                sortname: $sortname,
				$eventosGrid
                sortorder: $sortorder $subGridAdd
				
            };
	
	var constantes={
		        datatype: "local",
				loadonce: true,
		        colModel: cm,
		        autoResizing: { compact: true },
                onSelectRow: function (id, isSelected) {
					$EonSelectRow
                    updateIdsOfSelectedRows(id, isSelected);
                    saveColumnState.call(mygrid, mygrid[0].p.remapColumns);
                },
				
                sortable: {
                    update: function () {
                        saveColumnState.call(mygrid);
                    },
                    options: {
                        opacity: 0.8
                    }
                },
                onSelectAll: function (aRowids, isSelected) {
					$EonSelectAll
                    var i, count, id;
                    for (i = 0, count = aRowids.length; i < count; i++) {
                        id = aRowids[i];
                        updateIdsOfSelectedRows(id, isSelected);
                    }
                    saveColumnState.call(mygrid, mygrid[0].p.remapColumns);
                },
				gridComplete:function(){
						

				},
                loadComplete: function () {
					$Eonload
				
					var selCols=$selctores;
					$.each(selCols,function(i,v){
						var textAll=v.textAll
						delete v.textAll;
						setSearchSelect(i,v,textAll);
				
						
						    var datafromgrid = mygrid.jqGrid('getRowData');
							rowIds = mygrid.jqGrid('getDataIDs');
							
							
							for(var o=0,len=rowIds.length;o<len;o++){
								 var p = rowIds[o];//idRow
								 var id = datafromgrid[o][i]; var n=v[id];
								 mygrid.jqGrid("setCell", p, i, n);
							}
					});
					
					
                    var thisgrid = $(this), p = thisgrid.jqGrid("getGridParam"), i, count;

                    if (firstLoad) {
                        firstLoad = false;
                        if (isColState && myColumnsState.cmOrder != null && myColumnsState.cmOrder.length > 0) {
                            // We compares the values from myColumnsState.cmOrder array
                            // with the current names of colModel and remove wrong names. It could be
                            // required if the column model are changed and the values from the saved stated
                            // not corresponds to the 
                            var fixedOrder = $.map(myColumnsState.cmOrder, function (name) {
                                    return p.iColByName[name] === undefined ? null : name;
                                });
                            thisgrid.jqGrid("remapColumnsByName", fixedOrder, true);
                        }
                        if (typeof (this.ftoolbar) !== "boolean" || !this.ftoolbar) {
                            // create toolbar if needed
                            thisgrid.jqGrid("filterToolbar",
                                {stringResult: true, searchOnEnter: $buscarAlEnter, defaultSearch: myDefaultSearch});
                        }
                    }
                    refreshSerchingToolbar(thisgrid, myDefaultSearch);
                    for (i = 0, count = idsOfSelectedRows.length; i < count; i++) {
                        thisgrid.jqGrid("setSelection", idsOfSelectedRows[i], false);
                    }
                    saveColumnState.call(thisgrid, this.p.remapColumns);
                },
                resizeStop: function () {
                    saveColumnState.call(mygrid, mygrid[0].p.remapColumns);
                }
	};
	
	$.extend( opciones, constantes );
	$.extend( opciones, userOpt );
	 
	mygrid.jqGrid(opciones);
	
	

	
			
			$.extend($.jgrid.search, {
                multipleSearch: true,
                multipleGroup: true,
                recreateFilter: true,
                closeOnEscape: true,
                closeAfterSearch: true
            });

			
            mygrid.jqGrid("navGrid", '#$pagerName',$jsonNavOpt,$jsonOptForms);
			
			 
			var newBtns=$jsonBtnCustomDefault;
			
			$.each(newBtns,function(i,v){
				
				if(v==true){
					switch(i){
						case "storeClear": 
							mygrid.jqGrid("navButtonAdd", {
								caption: "",
								buttonicon: "fa-times",
								title: "Limpia las configuracion guardadas",
								onClickButton: function () {
									removeObjectFromLocalStorage(myColumnStateName($(this)));
									window.location.reload();
								}
							});
						break;
						case "cols": 
								mygrid.jqGrid("navButtonAdd", {
									caption: "",
									buttonicon: "fa-table",
									title: "Selecciona las columnas a ver",
									onClickButton: function () {
										$(this).jqGrid("columnChooser", {
											done: function (perm) {
												if (perm) {
													this.jqGrid("remapColumns", perm, true);
													saveColumnState.call(this);
												}
											}
										});
									}
								});
						break;
						case "refresh":
			
							mygrid.jqGrid("navButtonAdd", {
								caption: "",
								buttonicon: "fa-refresh",
								title: "Reload",
								onClickButton: function () {
								   $("#$divContenedora").load("$myurl");
								}
							});
							
						break;
					}
				}
				
			})
			
		
			

			//Controlador de buscadores
			mygrid.bind("jqGridToolbarBeforeSearch", function (e, rowid, orgClickEvent){
			      $("#$divContenedora").load("$myurl");
			});

			
			mygrid.bind("jqGridFilterSearch", function (e, rowid, orgClickEvent){
			      $("#$divContenedora").load("$myurl");
			});
			//Eventos de Usuario
              $jqEvent
			
			
//Funciones de Usuario 			  
			 $jsAddGrid 
//Fin de Funciones de Usuario			 
	
//Botones de usuario

var botones=$botones;


$.each(botones,function(i,v){
	
		mygrid.jqGrid("navButtonAdd", v);
			
});
//Fin de botones de usuario	
	
	
});//Fin del Ready
</script>
INITJS;

echo $initjs;

$html=<<<HTML
    <div id="outerDiv_$mygridName" style="margin: 5px;">
        <table id="$mygridName"></table>
		<div id='$pagerName'></div>
    </div>
HTML;
	echo $html;
}
	
	
	
	
}

?>