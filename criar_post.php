<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'conexao.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'conexao.php';

// ADICIONE ESTAS LINHAS PARA TESTAR A CONEXÃO
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
} else {
    echo "Conexão com o banco de dados OK.<br>";
}
// FIM DAS LINHAS DE TESTE

$conteudo = '';
if (isset($_POST['conteudo'])) {
    $conteudo = $conn->real_escape_string($_POST['conteudo']);
}



$conteudo = '';
if (isset($_POST['conteudo'])) {
    $conteudo = $conn->real_escape_string($_POST['conteudo']);
}

$id_usuario = $_SESSION['id'];
$imagem_url = NULL; // Inicializa como NULL, será preenchido se uma imagem for enviada

// Verifica se um arquivo de imagem foi enviado
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "uploads/"; // Pasta onde as imagens serão salvas
    // Cria a pasta 'uploads' se ela não existir
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Permissões 0777 são amplas, ajuste conforme segurança
    }

    $imageFileType = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    // Gera um nome único para o arquivo para evitar colisões
    $new_file_name = uniqid() . "." . $imageFileType;
    $target_file = $target_dir . $new_file_name;

    // Validações básicas da imagem
    $check = getimagesize($_FILES['imagem']['tmp_name']);
    if ($check === false) {
        echo "O arquivo não é uma imagem válida.";
        exit;
    }
    if ($_FILES['imagem']['size'] > 5000000) { // Limite de 5MB (5 * 1024 * 1024 bytes)
        echo "Desculpe, seu arquivo é muito grande. Tamanho máximo: 5MB.";
        exit;
    }
    if (!in_array($imageFileType, $allowed_types)) {
        echo "Desculpe, apenas JPG, JPEG, PNG e GIF são permitidos.";
        exit;
    }

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_file)) {
        $imagem_url = $conn->real_escape_string($target_file); // Salva o caminho relativo
    } else {
        echo "Erro ao fazer upload da imagem. Código de erro: " . $_FILES['imagem']['error'];
        exit;
    }
}

// Verifica se há conteúdo de texto OU imagem
if (empty($conteudo) && is_null($imagem_url)) {
    echo "Conteúdo da postagem ou imagem está vazio. Por favor, adicione texto ou uma imagem.";
    exit;
}

// Insere no banco de dados
$sql = "INSERT INTO postagens (id_usuario, conteudo, imagem_url) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $id_usuario, $conteudo, $imagem_url); // "iss" para int, string, string (ou null)

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Erro ao inserir: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>