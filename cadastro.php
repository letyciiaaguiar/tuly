<?php
include 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados.
$mensagem = ""; // Inicializa a variável de mensagem para feedback ao usuário.

// Verifica se a requisição é do tipo POST (ou seja, o formulário foi enviado).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Escapa caracteres especiais no e-mail para evitar SQL Injection.
    $email = $conn->real_escape_string($_POST['email']);
    // Gera o hash SHA1 da senha para segurança.
    $senha = sha1($_POST['senha']);

    // Verifica se o e-mail já está cadastrado no banco de dados.
    $verificar = $conn->query("SELECT * FROM usuarios WHERE email = '$email'");
    if ($verificar->num_rows > 0) { // Se o e-mail já existe.
        $mensagem = "Esse e-mail já está cadastrado."; // Define a mensagem de erro.
    } else {
        // Insere o novo usuário no banco de dados.
        $sql = "INSERT INTO usuarios (email, senha) VALUES ('$email', '$senha')";
        if ($conn->query($sql)) { // Se a inserção for bem-sucedida.
            header("Location: login.php"); // Redireciona para a página de login.
            exit; // Garante que o script pare de executar após o redirecionamento.
        } else {
            $mensagem = "Erro ao cadastrar: " . $conn->error; // Define a mensagem de erro do banco de dados.
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Tuly</title>
    <link rel="stylesheet" href="cadastro.css">
</head>
<body>
    <div class="container">
        <div class="left-side"></div>
        <div class="right-side">
            <h2>Cadastre-se</h2>

            <?php if ($mensagem): // Exibe a mensagem de feedback se ela não estiver vazia. ?>
                <p class="mensagem" style="color: red; margin-bottom: 15px; text-align: center;"><?php echo $mensagem; ?></p>
            <?php endif; ?>

            <form method="POST" action="cadastro.php">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Seu e-mail" required>

                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Sua senha" required>

                <button type="submit">Cadastrar</button>
            </form>

            <p style="color: #eee; text-align: center; margin-top: 20px; font-size: 14px;">
                Já tem conta? <a href="login.php" style="color: #D37DFF; text-decoration: none; font-weight: 500;">Faça login</a>
            </p>
        </div>
    </div>
</body>
</html>