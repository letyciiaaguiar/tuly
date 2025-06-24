<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$postagem = null;

// Se for GET (abrir o formulário)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $id_usuario = $_SESSION['id'];

    $sql = "SELECT * FROM postagens WHERE id = $id AND id_usuario = $id_usuario";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $postagem = $resultado->fetch_assoc();
    } else {
        echo "Postagem não encontrada ou sem permissão.";
        exit;
    }
}

// Se for POST (enviar edição)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conteudo = $conn->real_escape_string($_POST['conteudo']);
    $id_usuario = $_SESSION['id'];

    $sql = "UPDATE postagens SET conteudo = '$conteudo' WHERE id = $id AND id_usuario = $id_usuario";
    if ($conn->query($sql)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Erro ao atualizar: " . $conn->error;
        exit;
    }
}
?>

<?php if ($postagem): ?>
<!DOCTYPE html>
<html>
<head><title>Editar Postagem</title></head>
<body>
    <h2>Editar Postagem</h2>
    <form method="POST" action="editar_post.php">
        <input type="hidden" name="id" value="<?php echo $postagem['id']; ?>">
        <textarea name="conteudo" rows="4" cols="50"><?php echo $postagem['conteudo']; ?></textarea><br>
        <button type="submit">Salvar</button>
        <a href="dashboard.php">Cancelar</a>
    </form>
</body>
</html>
<?php endif; ?>
