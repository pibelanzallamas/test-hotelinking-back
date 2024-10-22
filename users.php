<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'database.php';

$data = json_decode(file_get_contents('php://input'), true);

//ruta para crear un usuario (register)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['register'])) {
    $name = $data['nombre'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([':name' => $name, ':email' => $email, ':password' => $password]);
        
        $lastInsertId = $pdo->lastInsertId();

        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $lastInsertId]);

        $newUser = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(["message" => "Usuario registrado con éxito", "user" => $newUser]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error al registrar usuario: " . $e->getMessage()]);
    }
}

//ruta para loguear un usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['login'])) {
    $email = $data['email'];
    $password = $data['password'];

    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(["message" => "Login exitoso", "user" =>$user]);
    } else {
        echo json_encode(["message" => "Credenciales inválidas"]);
    }
}


?>