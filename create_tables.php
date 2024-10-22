<?php
include 'database.php';

//crea la tabla 'users'
$createUsersTable = "
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
";

//crea la tabla 'codes'
$createCodesTable = "
CREATE TABLE IF NOT EXISTS codes (
    id SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    code VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    uid INT REFERENCES users(id),
    state BOOLEAN DEFAULT FALSE
);
";

try {
    $pdo->exec($createUsersTable);
    echo "Tabla 'users' creada con éxito.<br>";
    
    $pdo->exec($createCodesTable);
    echo "Tabla 'codes' creada con éxito.<br>";
} catch (PDOException $e) {
    echo "Error al crear las tablas: " . $e->getMessage();
}
?>
