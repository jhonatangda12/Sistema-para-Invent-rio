<?php
$host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$db   = getenv('MYSQLDATABASE') ?: 'railway'; // Ajustado para MYSQL_DATABASE com underline
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'luZFmXafxTvpgUUoFshwmWmDjaIaNtYh';
$port = getenv('MYSQLPORT') ?: '3306';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}
?>
