<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'conexao.php';

$id_usuario = $_SESSION['id'];
$email_usuario = $_SESSION['email'];

// Buscar dados do perfil do usuário do banco de dados, incluindo a foto_perfil_url
$sql_perfil = "SELECT email, foto_perfil_url FROM usuarios WHERE id = $id_usuario";
$resultado_perfil = $conn->query($sql_perfil);
$dados_perfil = $resultado_perfil->fetch_assoc();

// Define a foto de perfil. Se não houver URL, usa um placeholder genérico (sem texto visível)
// Você pode usar uma imagem local de um avatar padrão, ou apenas uma cor de fundo com CSS.
$foto_perfil_atual = !empty($dados_perfil['foto_perfil_url']) ? htmlspecialchars($dados_perfil['foto_perfil_url']) : 'https://via.placeholder.com/120/5A5A5A/FFFFFF?text='; 
// Usando 'https://via.placeholder.com/120/5A5A5A/FFFFFF?text=' para um círculo cinza sem texto.
// Ou um caminho para uma imagem local como 'assets/default_avatar.png'
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Tuly</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ... seu estilo existente no <style> do perfil.php ... */
        /* Mantenha o estilo do .profile-avatar-upload e .profile-avatar-upload label aqui ou no dashboard.css */
        /* Preferencialmente no dashboard.css para consistência */
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <i class="fa-solid fa-g"></i>
            </div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-item"><i class="fa-solid fa-house"></i></a>
                <a href="#createPostSection" class="nav-item add-button-sidebar" id="newPostButtonProfile"><i class="fa-solid fa-plus"></i></a>
                <a href="perfil.php" class="nav-item active"><i class="fa-solid fa-user"></i></a>
            </nav>
            <div class="menu-icon" id="hamburgerMenuIconProfile">
                <i class="fa-solid fa-bars"></i>
            </div>
        </aside>

        <div class="off-canvas-menu" id="offCanvasMenuProfile">
            <div class="menu-header">
                <h3>Opções</h3>
                <button class="close-menu-btn" id="closeMenuBtnProfile"><i class="fa-solid fa-times"></i></button>
            </div>
            <nav class="menu-nav">
                <a href="gerar_pdf.php" class="menu-item"><i class="fa-solid fa-file-pdf"></i> Baixar PDF</a>
                <a href="logout.php" class="menu-item"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
            </nav>
        </div>
        <div class="menu-overlay" id="menuOverlayProfile"></div>

        <main class="main-content">
            <header class="top-bar">
                <span>Meu Perfil</span>
            </header>

            <div class="profile-container">
                <h2>Seu Perfil</h2>
                <form id="profilePhotoUploadForm" action="upload_foto_perfil.php" method="POST" enctype="multipart/form-data">
                    <div class="profile-avatar-upload">
                        <img src="<?php echo $foto_perfil_atual; ?>" alt="Foto de Perfil" id="profileImagePreview">
                        <input type="file" id="profilePhotoInput" name="foto_perfil" accept="image/*">
                        <label for="profilePhotoInput"><i class="fa-solid fa-camera"></i></label>
                    </div>
                    <button type="submit" class="publish-button" style="margin-top: 15px;">Salvar Foto</button>
                </form>

                <div class="profile-info">
                    <p>Email: <span><?php echo htmlspecialchars($email_usuario); ?></span></p>
                    <p>Membro desde: <span><?php // Aqui você pode adicionar a data de cadastro do usuário ?></span></p>
                </div>
                <a href="dashboard.php" class="back-button">Voltar para o Dashboard</a>
            </div>
        </main>
    </div>

    <script>
        // Lógica para pré-visualização da foto de perfil
        document.addEventListener('DOMContentLoaded', () => {
            const profilePhotoInput = document.getElementById('profilePhotoInput');
            const profileImagePreview = document.getElementById('profileImagePreview');

            if (profilePhotoInput && profileImagePreview) {
                profilePhotoInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            profileImagePreview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Lógica para o Menu Off-Canvas (Hambúrguer) na página de perfil
            const hamburgerIcon = document.getElementById('hamburgerMenuIconProfile');
            const offCanvasMenu = document.getElementById('offCanvasMenuProfile');
            const closeMenuBtn = document.getElementById('closeMenuBtnProfile');
            const menuOverlay = document.getElementById('menuOverlayProfile');

            function toggleMenu() {
                offCanvasMenu.classList.toggle('active');
                menuOverlay.classList.toggle('active');
                document.body.classList.toggle('menu-open-mobile');
            }

            if (hamburgerIcon && offCanvasMenu && closeMenuBtn && menuOverlay) {
                hamburgerIcon.addEventListener('click', toggleMenu);
                closeMenuBtn.addEventListener('click', toggleMenu);
                menuOverlay.addEventListener('click', toggleMenu);
            }

            // Para o botão de nova postagem na página de perfil
            const newPostButtonProfile = document.getElementById('newPostButtonProfile');
            if (newPostButtonProfile) {
                 newPostButtonProfile.addEventListener('click', (e) => {
                    e.preventDefault();
                    window.location.href = 'dashboard.php#createPostSection';
                });
            }
        });
    </script>
</body>
</html>