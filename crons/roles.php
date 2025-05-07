<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
//print_r($_ENV);
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

    // Consultar los datos de la tabla usr
    $stmt = $pdo->query("SELECT idusers_roles, nombre FROM users_roles");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si la consulta devolvió resultados
    if (empty($users)) {
        echo "No se encontraron datos en la base de datos.";
        exit;
    }

    // Crear un nuevo archivo Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Definir encabezados
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Nombre');

    // Insertar los datos
    $row = 2; // Empezar en la segunda fila
    foreach ($users as $user) {
        $sheet->setCellValue('A' . $row, $user['idusers_roles']);
        $sheet->setCellValue('B' . $row, $user['nombre']);
        $row++;
    }

    // Crear y guardar el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $fileName = 'roles_export.xlsx';
    $writer->save($fileName);

    echo "Archivo Excel '$fileName' generado con éxito.";


?>
