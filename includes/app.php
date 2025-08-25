<?php

require 'funciones.php';
require 'database.php';
require __DIR__ . '/../vendor/autoload.php';
// config.php
define('BASE_URL', '/PORTAL_COBROS_PROD/public');


// Conectarnos a la base de datos
use Model\ActiveRecord;
use Model\Usuario;

ActiveRecord::setMySQLDB($mysql_db);     // Configurar la conexión a MySQL
ActiveRecord::setSQLSrvDB($sqlsrv_conn); // Configurar la conexión a SQL Server
ActiveRecord::setSQLSrvDB2($sqlsrv_conn2);
// Puedes elegir la conexión activa
ActiveRecord::useMySQL(); // O usa ActiveRecord::useSQLSrv() para cambiar entre bases de datos

Usuario::useSQLSrv();
