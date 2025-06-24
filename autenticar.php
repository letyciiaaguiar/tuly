<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();
include 'conexao.php';

$email = $_POST['email'];
$senha = sha1($_POST['senha']);

$sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
    $_SESSION['id'] = $usuario['id'];
    $_SESSION['email'] = $usuario['email'];
    header("Location: dashboard.php");
} else {
    echo "Login inválido!";
}
?>
