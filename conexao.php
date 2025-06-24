
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "rede_social";
$porta = "3307";

$conn = new mysqli($host, $usuario, $senha, $banco, $porta);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>

