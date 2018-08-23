<?php
//Mysql Info
// define('DB_HOST','lolfantasy.x10.mx');//IP Produccion
// define('DB_NAME','lolfant2_lolfantasy');
// define('DB_USER','lolfant2_ragde');//User Produccion 
// define('DB_PASSWORD','784512');
// define('DB_PORT',3306);

define('DB_HOST','localhost');//IP Produccion
define('DB_NAME','prueba');
define('DB_USER','root');//User Produccion 
define('DB_PASSWORD','784512');
define('DB_PORT','3306');
//define('DB_PORT','3306');


define("URL","http://lolfantasy.esy.es");

//Mail Server Info
define('DEBUGMAIL',false);//Si quieres ver la respuesta de la libreria
define('SMTPON',true);//Si esta activado se usar el servicio de conexion smtp, esto depende del host
define('SERVERMAIL','smtp.gmail.com');//Servidor SMTP
define('PORTMAIL',587);///Puerto del servidor SMTP
define('USERMAIL','zeroxragde656@gmail.com');//Usuario
define('PASSMAIL','darkpower4ever');//Clave

//SERVER Mail  
define('HOSTMAIL','admin@lolfantasy.x10.mx');//Correo desde el que se envia el mensaje en host gratis debe ser el que des de alta
define("REPLYMAIL",'noreplay@lolfantasy.x10.mx');
define("NOMBRE",'Administrador');


 
?>