<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'database.php';

$data = json_decode(file_get_contents('php://input'), true);

//crea el codigo de un producto en la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['create'])) {
    $uid = $data['uid'];
    $title = $data['title'];
    $price = $data['price'];
    $code = $data['code'];

    $sql = "INSERT INTO codes (uid, title, price, code) VALUES (:uid, :title, :price, :code)";
    $stmt = $pdo->prepare($sql);

    try {
        $success = $stmt->execute([
            ':uid' => $uid,
            ':title' => $title,
            ':price' => $price,
            ':code' => $code,
        ]);

        if ($success) {
            $lastId = $pdo->lastInsertId();  
            
            $query = "SELECT * FROM codes WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id' => $lastId]);
            $newCode = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "message" => "Código creado exitosamente",
                "finalCode" => $newCode 
            ]);
        } else {
            echo json_encode(["error" => "Error al crear el código"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error al registrar usuario: " . $e->getMessage()]);
    }
}

//chequea si un producto ya tiene código de acuerdo a un usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['checkTitle'])) {
    $title = $data['title'];
    $uid = $data['uid']; 

    $sql = "SELECT * FROM codes WHERE title = :title AND uid = :uid";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([':title' => $title, ':uid' => $uid]);
        $promo = $stmt->fetch(PDO::FETCH_ASSOC);  

        if ($promo) {
            echo json_encode(["promo" => $promo]);
        } else {
            echo json_encode(["promo" => null]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error al verificar el título y UID: " . $e->getMessage()]);
    }
}

//devuelve todas los códigos de un usuario 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['getByUid'])) {
    $uid = $data['uid'];

    $sql = "SELECT * FROM codes WHERE uid = :uid ORDER BY id ASC";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([':uid' => $uid]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            echo json_encode(["codes" => $rows]);
        } else {
            echo json_encode(["codes" => []]); 
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error al obtener las filas por UID: " . $e->getMessage()]);
    }
}

// actualiza el estado de un código
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['updateState'])) {
    $code = $data['code'];

    $sql = "UPDATE codes SET state = true WHERE code = :code";
    $stmt = $pdo->prepare($sql);

    try {
        $success = $stmt->execute([':code' => $code]);

        if ($success) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(["mod" => true]);
            } else {
                echo json_encode(["mod" => false]);
            }
        } else {
            echo json_encode(["error" => "Error al actualizar el estado"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error al actualizar el estado: " . $e->getMessage()]);
    }
}

//actualiza el estado de todos los códigos de un usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['updateAllStates'])) {
    $uid = $data['uid'];

    $sql = "UPDATE codes SET state = true WHERE uid = :uid";
    $stmt = $pdo->prepare($sql);

    try {
        $success = $stmt->execute([':uid' => $uid]);

        if ($success) {
            echo json_encode(["mod" => true]);
        } else {
            echo json_encode(["mod" =>false]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error al actualizar los estados: " . $e->getMessage()]);
    }
}

?>
