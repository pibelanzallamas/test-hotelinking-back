<?php
$host = 'localhost';
$dbname = 'hotel';
$user = 'postgres';  
$pass = ''; 

try {
    //conexion a bdd
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
    //configuracion del modo de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    //mensaje de error
    echo "Error de conexión: " . $e->getMessage();
}
?>
