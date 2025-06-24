<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'conexao.php';

$id_usuario = $_SESSION['id'];

// Consulta que busca o conteúdo, a data e o e-mail do autor
$sql = "SELECT p.*, u.email FROM postagens p
        JOIN usuarios u ON p.id_usuario = u.id
        WHERE p.id_usuario = $id_usuario
        ORDER BY p.data_postagem DESC";

$resultado = $conn->query($sql);

// Monta o HTML do PDF
$html = "<h1 style='text-align:center;'>Minhas Postagens</h1>";

while ($p = $resultado->fetch_assoc()) {
    $dataHora = date('d/m/Y H:i', strtotime($p['data_postagem']));
    $html .= "
        <div style='margin-bottom: 20px; padding: 10px; border: 1px solid #ccc;'>
            <p>" . nl2br($p['conteudo']) . "</p>
            <small>Postado por <strong>{$p['email']}</strong> em {$dataHora}</small>
        </div>
    ";
}

// Gera o PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("minhas_postagens.pdf", ["Attachment" => true]);
?>
