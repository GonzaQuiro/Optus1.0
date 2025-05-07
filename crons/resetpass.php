<?php

require __DIR__ . '/../vendor/autoload.php';

// Conexión a la base de datos
function loadEnv($file) {
    if (file_exists($file)) {
        $lines = file($file);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line && $line[0] !== '#' && strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                if (!empty($key)) {
                    $_ENV[$key] = $value;
                }
            }
        }
    }
}

// Cargar el archivo .env
loadEnv(__DIR__ . '/../.env');

try {
    // Crear una instancia de PDO usando las variables de entorno
    $dsn = "mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_NAME');
    $pdo = new PDO($dsn, env('DB_USERNAME'), env('DB_PASSWORD'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<br>"."Acceso correcto a la BBDD"."<br>";
} 
catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Obtener la fecha actual y restar 3 meses
$currentDate = new DateTime();
$threeMonthsAgo = $currentDate->modify('-3 months')->format('Y-m-d');

// Consulta para obtener todos los usuarios cuya pass_date o created_at sea mayor o igual a 3 meses
$sql = "
    SELECT id, pass_date, created_at 
    FROM users 
    WHERE 
        (pass_date IS NOT NULL AND pass_date <= :threeMonthsAgo) 
        OR (pass_date IS NULL AND created_at <= :threeMonthsAgo)
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':threeMonthsAgo', $threeMonthsAgo);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Actualizar el atributo pass_change a 'S' y pass_date con la fecha actual para los usuarios seleccionados
if ($users) {
    foreach ($users as $user) {
        // Actualizar el campo pass_change y pass_date con la fecha actual
        // Actualizar solo el campo pass_change
        $updateSql = "UPDATE users SET pass_change = 'S' WHERE id = :id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':id', $user['id']);
        $updateStmt->execute();
    }
    echo "Se ha actualizado el atributo 'pass_change' para los usuarios que requieren cambio de contraseña.\n";
} else {
    echo "No hay usuarios que requieran actualización de contraseña.\n";
}

?>
