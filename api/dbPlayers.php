<?php
require 'vendor/autoload.php';

// Carrega as variáveis do .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$host = 'localhost';
$db   = 'u711956194_BibliaQuiz';
$user = 'u711956194_cristao';
$pass = 'B1bl14_2023';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    die(json_encode(["erro" => "Falha na conexão com o banco"]));
}
?>
<!-- M3sm0qu33u4nd3p0rumv4l3d3d3ns4str3v4sn40t3m3r31p3r1g04lgump01stu3st4sc0m1g04tu4v4r43 0t3uc4j4d0m3c0nf0rt4m -->