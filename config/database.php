<?php
declare(strict_types = 1);

$host = 'localhost';
$dbname = 'afec_v2';
$user = 'root';
$pass = '';
$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try{
    $pdo = new PDO($dsn, $user, $pass, $options);
}catch(PDOException $e){
    error_log('Error de conexion a AFEC_LAP V2: ' . $e->getMessage() );
    die('No fue posibel establecer conexion con la base de datos');
}


?>