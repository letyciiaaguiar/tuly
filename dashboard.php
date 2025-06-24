<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'conexao.php'; // Inclua sua conexão com o banco de dados.

// --- Adicionado para obter a foto de perfil do usuário logado ---
$id_usuario_logado = $_SESSION['id'];
$sql_usuario_logado = "SELECT email, foto_perfil_url FROM usuarios WHERE id = $id_usuario_logado";
$result_usuario_logado = $conn->query($sql_usuario_logado);
$dados_usuario_logado = $result_usuario_logado->fetch_assoc();

$email_usuario_logado = $dados_usuario_logado['email']; // Já estava em $_SESSION, mas pegamos para consistência
$foto_perfil_usuario_logado = $dados_usuario_logado['foto_perfil_url'] ?? 'https://via.placeholder.com/40?text=Avatar';
// -----------------------------------------------------------------


// Consulta com JOIN para pegar o e-mail do usuário e a foto de perfil junto com as postagens
$sql = "SELECT p.*, u.email, u.foto_perfil_url FROM postagens p
        JOIN usuarios u ON p.id_usuario = u.id
        WHERE p.id_usuario = $id_usuario_logado
        ORDER BY p.data_postagem DESC";
$postagens = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Rede Social</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="tuly.png"Ícone da Tuly"> 
            </div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-item active"><i class="fa-solid fa-house"></i></a>
                
                <a href="#createPostSection" class="nav-item add-button-sidebar" id="newPostButton"><i class="fa-solid fa-plus"></i></a>
                
                <a href="perfil.php" class="nav-item"><i class="fa-solid fa-user"></i></a>
            </nav>
            <div class="menu-icon" id="hamburgerMenuIcon">
                <i class="fa-solid fa-bars"></i>
            </div>
        </aside>

        <div class="off-canvas-menu" id="offCanvasMenu">
            <div class="menu-header">
                <h3>Opções</h3>
                <button class="close-menu-btn" id="closeMenuBtn"><i class="fa-solid fa-times"></i></button>
            </div>
            <nav class="menu-nav">
                <a href="gerar_pdf.php" class="menu-item"><i class="fa-solid fa-file-pdf"></i> Baixar PDF</a>
                <a href="logout.php" class="menu-item"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
            </nav>
        </div>
        <div class="menu-overlay" id="menuOverlay"></div>

        <main class="main-content">
            <header class="top-bar">
                <span>Para ti</span>
                <i class="fa-solid fa-chevron-down"></i>
            </header>

            <section class="create-post-section" id="createPostSection">
                <div class="post-input-card">
                    <div class="user-avatar-small">
                        <img src="<?php echo htmlspecialchars($foto_perfil_usuario_logado); ?>" alt="">
                    </div>
                    <form id="createPostForm" action="criar_post.php" method="POST" enctype="multipart/form-data">
                        <div class="input-area-with-file">
                            <textarea name="conteudo" rows="3" placeholder="O que há de novo?"></textarea>
                            <div class="image-upload-preview">
                                <input type="file" id="imageUpload" name="imagem" accept="image/*" style="display: none;">
                                <label for="imageUpload" class="upload-icon-label">
                                    <i class="fa-solid fa-image"></i>
                                </label>
                                <img id="" src="#" alt="" style="display: none;">
                            </div>
                        </div>
                        <button type="submit" class="publish-button">Publicar</button>
                    </form>
                </div>
            </section>

            <section class="posts-feed">
                <?php if ($postagens->num_rows > 0): ?>
                    <?php while ($p = $postagens->fetch_assoc()): ?>
                        <div class="post-card">
                            <div class="post-header">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <img src="<?php echo htmlspecialchars($p['foto_perfil_url'] ?? 'https://via.placeholder.com/50?text=Avatar'); ?>" alt="">
                                    </div>
                                    <div>
                                        <span class="username"><?php echo htmlspecialchars($p['email']); ?></span>
                                        <span class="time-ago"><?php echo date('d/m/Y H:i', strtotime($p['data_postagem'])); ?></span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-ellipsis"></i>
                            </div>
                            <div class="post-body">
                                <p><?php echo nl2br(htmlspecialchars($p['conteudo'])); ?></p>
                                <?php if (!empty($p['imagem_url'])): ?>
                                    <div class="post-images">
                                        <img src="<?php echo htmlspecialchars($p['imagem_url']); ?>" alt="Imagem da postagem">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="post-actions">
                                <i class="far fa-comment"></i> <span>0</span>
                                <i class="fa-regular fa-bookmark"></i>
                                <i class="fa-regular fa-paper-plane"></i>
                                <i class="fa-solid fa-chart-simple"></i>
                            </div>
                            <div class="post-actions-edit-delete">
                                <a href="editar_post.php?id=<?php echo $p['id']; ?>">Editar</a> |
                                <a href="excluir_post.php?id=<?php echo $p['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Você ainda não tem postagens.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <button class="fab-button" id="fabButton">
        <i class="fa-solid fa-plus"></i>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Lógica para pré-visualização da imagem na criação de post
            const imageUploadInput = document.getElementById('imageUpload');
            const imagePreview = document.getElementById('imagePreview');
            if (imageUploadInput && imagePreview) {
                imageUploadInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.src = '#';
                        imagePreview.style.display = 'none';
                    }
                });
            }

            // Lógica para o Menu Off-Canvas (Hambúrguer) - PDF e Sair
            const hamburgerIcon = document.getElementById('hamburgerMenuIcon');
            const offCanvasMenu = document.getElementById('offCanvasMenu');
            const closeMenuBtn = document.getElementById('closeMenuBtn');
            const menuOverlay = document.getElementById('menuOverlay');

            function toggleMenu() {
                offCanvasMenu.classList.toggle('active');
                menuOverlay.classList.toggle('active');
                document.body.classList.toggle('menu-open-mobile'); // Para controlar o scroll do body em mobile
            }

            if (hamburgerIcon && offCanvasMenu && closeMenuBtn && menuOverlay) {
                hamburgerIcon.addEventListener('click', toggleMenu);
                closeMenuBtn.addEventListener('click', toggleMenu);
                menuOverlay.addEventListener('click', toggleMenu); // Fecha ao clicar no overlay
            }

            // Lógica para ROLAR para a seção de criar post quando o botão '+' é clicado
            const newPostButton = document.getElementById('newPostButton'); // Botão '+' na sidebar
            const createPostSection = document.getElementById('createPostSection'); // A seção inteira de criar post
            const fabButton = document.getElementById('fabButton'); // O Floating Action Button

            function scrollToCreatePostSection() {
                if (createPostSection) {
                    createPostSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
            
            if (newPostButton) {
                newPostButton.addEventListener('click', (e) => {
                    e.preventDefault(); // Impede o comportamento padrão do link (#)
                    scrollToCreatePostSection();
                });
            }

            if (fabButton) {
                fabButton.addEventListener('click', scrollToCreatePostSection);
            }
            
            // Opcional: Limpar a área de criar post ao publicar com sucesso
            const createPostForm = document.getElementById('createPostForm');
            if (createPostForm) {
                createPostForm.addEventListener('submit', () => {
                    // Pequeno atraso para permitir que a submissão aconteça antes de limpar
                    setTimeout(() => {
                        const textarea = document.querySelector('.create-post-section textarea');
                        if (textarea) textarea.value = '';
                        
                        const imageUpload = document.getElementById('imageUpload');
                        if (imageUpload) imageUpload.value = '';
                        
                        const imagePrev = document.getElementById('imagePreview');
                        if (imagePrev) imagePrev.style.display = 'none';
                    }, 100); 
                });
            }
        });
    </script>
</body>
</html>