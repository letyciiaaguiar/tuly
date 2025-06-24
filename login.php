<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="container">
    <div class="left-side"></div>
    <div class="right-side">
      <h2>Login</h2>
      <form action="autenticar.php" method="POST">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Entrar</button>
      </form>
    </div>
  </div>
</body>
</html>
