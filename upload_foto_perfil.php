<?php
error_reporting(E_ALL); // Exibe todos os erros
ini_set('display_errors', 1); // Garante que os erros sejam mostrados na tela

if (session_status() == PHP_SESSION_NONE) { // Inicia a sessão se não estiver ativa
    session_start();
}

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'conexao.php';

$id_usuario = $_SESSION['id'];

// --- DEBUGGING: Verifique o conteúdo de $_FILES e $_POST ---
echo "<pre>";
echo "Conteúdo de _FILES:<br>";
print_r($_FILES);
echo "Conteúdo de _POST:<br>";
print_r($_POST);
echo "</pre>";
// -----------------------------------------------------------

if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    // Adicione mais mensagens de depuração aqui
    echo "Arquivo 'foto_perfil' recebido.<br>";
    echo "Nome original: " . $_FILES['foto_perfil']['name'] . "<br>";
    echo "Tamanho: " . $_FILES['foto_perfil']['size'] . " bytes<br>";
    echo "Tipo: " . $_FILES['foto_perfil']['type'] . "<br>";
    echo "Nome temporário: " . $_FILES['foto_perfil']['tmp_name'] . "<br>";
    echo "Código de erro: " . $_FILES['foto_perfil']['error'] . "<br>"; // 0 = UPLOAD_ERR_OK

    $upload_dir = "uploads/perfil/"; // Subpasta para fotos de perfil
    
    // Cria a pasta 'uploads/perfil' se não existir
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            echo "Erro: Não foi possível criar o diretório de upload: " . $upload_dir . "<br>";
            exit;
        }
        echo "Diretório de upload criado: " . $upload_dir . "<br>";
    }

    $imageFileType = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    $new_file_name = $id_usuario . '_perfil_' . uniqid() . "." . $imageFileType;
    $target_file = $upload_dir . $new_file_name;

    // Validações
    $check = @getimagesize($_FILES['foto_perfil']['tmp_name']); // @ para suprimir avisos se o arquivo não for imagem
    if ($check === false) {
        echo "O arquivo não é uma imagem válida. (getimagesize falhou)<br>";
        exit;
    }
    if ($_FILES['foto_perfil']['size'] > 2000000) { // Limite de 2MB para foto de perfil
        echo "Desculpe, a foto de perfil é muito grande. Tamanho máximo: 2MB.<br>";
        exit;
    }
    if (!in_array($imageFileType, $allowed_types)) {
        echo "Desculpe, apenas JPG, JPEG, PNG e GIF são permitidos para fotos de perfil.<br>";
        exit;
    }

    // Antes de mover a nova imagem, verifica se já existe uma foto de perfil antiga e a remove
    $sql_get_old_photo = "SELECT foto_perfil_url FROM usuarios WHERE id = ?";
    $stmt_get_old = $conn->prepare($sql_get_old_photo);
    if ($stmt_get_old === false) {
        echo "Erro na preparação da consulta para buscar foto antiga: " . $conn->error . "<br>";
        exit;
    }
    $stmt_get_old->bind_param("i", $id_usuario);
    $stmt_get_old->execute();
    $stmt_get_old->bind_result($old_photo_url);
    $stmt_get_old->fetch();
    $stmt_get_old->close();

    if (!empty($old_photo_url) && file_exists($old_photo_url)) {
        if (unlink($old_photo_url)) {
            echo "Foto de perfil antiga removida: " . $old_photo_url . "<br>";
        } else {
            echo "Aviso: Não foi possível remover a foto de perfil antiga: " . $old_photo_url . "<br>";
        }
    }

    // Tenta mover a nova foto para o diretório de upload
    if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $target_file)) {
        echo "Arquivo movido para: " . $target_file . "<br>";
        $foto_perfil_url = $conn->real_escape_string($target_file);

        // Atualiza o caminho da foto de perfil no banco de dados para o usuário
        $sql_update = "UPDATE usuarios SET foto_perfil_url = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update === false) {
            echo "Erro na preparação da consulta UPDATE: " . $conn->error . "<br>";
            exit;
        }
        $stmt_update->bind_param("si", $foto_perfil_url, $id_usuario);

        if ($stmt_update->execute()) {
            echo "Caminho da foto de perfil atualizado no banco de dados.<br>";
            $_SESSION['foto_perfil_url'] = $foto_perfil_url; // Atualiza na sessão também
            header("Location: perfil.php?success=1"); // Redireciona com sucesso
            exit;
        } else {
            echo "Erro ao atualizar a foto de perfil no banco de dados: " . $stmt_update->error . "<br>";
            // Se falhar a atualização no DB, pode ser bom remover a imagem que foi movida
            if (file_exists($target_file)) {
                if (unlink($target_file)) {
                    echo "Arquivo temporário removido após erro no DB.<br>";
                }
            }
        }
        $stmt_update->close();

    } else {
        echo "Erro ao mover o arquivo para o destino final. Código de erro: " . $_FILES['foto_perfil']['error'] . "<br>";
        switch ($_FILES['foto_perfil']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                echo "O arquivo excede o upload_max_filesize do php.ini.<br>";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                echo "O arquivo excede o MAX_FILE_SIZE especificado no formulário HTML.<br>";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "O upload do arquivo foi feito apenas parcialmente.<br>";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "Nenhum arquivo foi enviado.<br>";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                echo "Pasta temporária não existe.<br>";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                echo "Falha ao gravar arquivo em disco.<br>";
                break;
            case UPLOAD_ERR_EXTENSION:
                echo "Uma extensão do PHP impediu o upload do arquivo.<br>";
                break;
            default:
                echo "Erro desconhecido no upload.<br>";
                break;
        }
    }
} else {
    echo "Nenhum arquivo de foto de perfil enviado ou erro inicial no upload.<br>";
    if (isset($_FILES['foto_perfil'])) {
        echo "Código de erro inicial: " . $_FILES['foto_perfil']['error'] . "<br>";
        switch ($_FILES['foto_perfil']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                echo "O arquivo excede o upload_max_filesize do php.ini.<br>";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                echo "O arquivo excede o MAX_FILE_SIZE especificado no formulário HTML.<br>";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "O upload do arquivo foi feito apenas parcialmente.<br>";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "Nenhum arquivo foi enviado.<br>";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                echo "Pasta temporária não existe.<br>";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                echo "Falha ao gravar arquivo em disco.<br>";
                break;
            case UPLOAD_ERR_EXTENSION:
                echo "Uma extensão do PHP impediu o upload do arquivo.<br>";
                break;
            default:
                echo "Erro desconhecido no upload.<br>";
                break;
        }
    }
}

$conn->close();
?>