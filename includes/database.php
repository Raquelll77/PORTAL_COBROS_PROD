<?php

//conexion a MySQL
$mysql_db = mysqli_connect('192.168.1.164', 'portal_web', 'Hk80dlezi0f6', 'movesa_garantias', 3306);

if (!$mysql_db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}

// Conexión a SQL Server usando PDO
$sqlsrv_db = "sqlsrv:Server=192.168.1.3;Database=MOVESAWeb";
$sqlsrv_user = 'Consultor01';
$sqlsrv_password = 'Sql1sapphp@!';

try {
    $sqlsrv_conn = new PDO($sqlsrv_db, $sqlsrv_user, $sqlsrv_password);
    $sqlsrv_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a SQL Server: " . $e->getMessage());
}

// Conexión a SQL Server usando PDO
$sqlsrv_db2 = "sqlsrv:Server=192.168.1.60;Database=SKG_BP";
$sqlsrv_user2 = 'sa';
$sqlsrv_password2 = 'S@pB1Sql';

try {
    $sqlsrv_conn2 = new PDO($sqlsrv_db2, $sqlsrv_user2, $sqlsrv_password2);
    $sqlsrv_conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a SQL Server: " . $e->getMessage());
}



// Conexión a SQL Server SAP MOVESA usando PDO
$sqlsrv_db3 = "sqlsrv:Server=192.168.1.3;Database=MOVESA";
$sqlsrv_user3 = 'Consultor01';
$sqlsrv_password3 = 'Sql1sapphp@!';

try {
    $sqlsrv_conn3 = new PDO($sqlsrv_db3, $sqlsrv_user3, $sqlsrv_password3);
    $sqlsrv_conn3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a SQL Server: " . $e->getMessage());
}
