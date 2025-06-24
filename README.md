# Tuly - Sua Rede Social Simplificada

Bem-vindo à Tuly, uma mini rede social desenvolvida com PHP, MySQL, HTML, CSS e JavaScript. Este projeto demonstra funcionalidades essenciais de uma plataforma social, como cadastro/login de usuários, criação de postagens (com texto e imagens), edição/exclusão de posts, e um perfil de usuário básico.

## Visão Geral

O objetivo deste projeto foi criar uma interface de usuário moderna e responsiva, com um tema escuro e funcionalidades interativas básicas.

**Funcionalidades Implementadas:**

* **Sistema de Autenticação:**
    * Cadastro de novos usuários.
    * Login seguro (senhas armazenadas com SHA1).
    * Controle de sessão para acesso restrito.
* **Gestão de Postagens:**
    * Criação de postagens com texto e/ou imagens.
    * Visualização de postagens na dashboard.
    * Edição e exclusão de postagens.
    * Geração de PDF com as postagens do usuário.
* **Perfil do Usuário:**
    * Página de perfil básica.
    * Funcionalidade de upload de foto de perfil.
* **Interface de Usuário:**
    * Design moderno com tema escuro.
    * Barra lateral de navegação (sidebar).
    * Menu hambúrguer (off-canvas) para opções adicionais (Baixar PDF, Sair).
    * Botão flutuante (FAB) para acesso rápido à criação de postagens.
    * Layout responsivo para diferentes tamanhos de tela.

## Tecnologias Utilizadas

* **Backend:** PHP
* **Banco de Dados:** MySQL/MariaDB
* **Frontend:** HTML5, CSS3, JavaScript
* **Dependências PHP:** Dompdf (para geração de PDF)
* **Ícones:** Font Awesome

## Como Rodar o Projeto Localmente

Para configurar e rodar este projeto em seu ambiente local, siga os passos abaixo:

### Pré-requisitos

Certifique-se de ter os seguintes softwares instalados em sua máquina:

* **Servidor Web (com PHP e MySQL/MariaDB):** Recomendado [XAMPP](https://www.apachefriends.org/pt_br/index.html) (Windows, Linux, macOS) ou [WAMP Server](https://www.wampserver.com/) (Windows).
* **Composer:** Gerenciador de dependências PHP. Baixe em [getcomposer.org](https://getcomposer.org/download/).
* **Cliente Git:** Para clonar o repositório. Baixe em [git-scm.com](https://git-scm.com/downloads).

### 1. Configuração do Banco de Dados

1.  **Inicie o MySQL/MariaDB e o Apache** no seu painel de controle do XAMPP/WAMP.
2.  **Acesse o phpMyAdmin:** Abra seu navegador e vá para `http://localhost/phpmyadmin`.
3.  **Crie o Banco de Dados:**
    * Clique na aba **"Bancos de dados"** ou **"Databases"**.
    * Crie um novo banco de dados com o nome `rede_social`.
4.  **Importe a Estrutura:**
    * Selecione o banco de dados `rede_social` recém-criado na barra lateral esquerda.
    * Clique na aba **SQL**.
    * Cole o conteúdo do arquivo `banco.sql` (disponível na raiz do projeto) na caixa de texto.
    * Clique em **"Executar"** (ou "Go"). Este script criará as tabelas `usuarios` e `postagens` e inserirá um usuário de teste (`admin@email.com` / `123456`).

    **`banco.sql` conteúdo:**
    ```sql
    CREATE DATABASE IF NOT EXISTS rede_social;
    USE rede_social;

    -- Tabela de usuários
    CREATE TABLE IF NOT EXISTS usuarios (
      id INT AUTO_INCREMENT PRIMARY KEY,
      email VARCHAR(100) NOT NULL UNIQUE,
      senha VARCHAR(255) NOT NULL,
      foto_perfil_url VARCHAR(255) NULL -- Adicionada para foto de perfil
    );

    -- Tabela de postagens
    CREATE TABLE IF NOT EXISTS postagens (
      id INT AUTO_INCREMENT PRIMARY KEY,
      id_usuario INT,
      conteudo TEXT NOT NULL,
      imagem_url VARCHAR(255) NULL, -- Adicionada para imagens nas postagens
      data_postagem TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
    );

    -- Usuário de teste (senha SHA1 de '123456')
    INSERT INTO usuarios (email, senha) VALUES ('admin@email.com', SHA1('123456'))
    ON DUPLICATE KEY UPDATE email = email;
    ```
5.  **Verifique as Credenciais do Banco:**
    * Abra o arquivo `conexao.php` e certifique-se de que as credenciais de conexão (host, usuário, senha, banco, porta) correspondem à sua configuração MySQL local.
        ```php
        <?php
        $host = "localhost";
        $usuario = "root";
        $senha = ""; // Se você usa senha no MySQL, ajuste aqui
        $banco = "rede_social";
        $porta = "3307"; // Porta padrão do MySQL no XAMPP, ajuste se for diferente

        $conn = new mysqli($host, $usuario, $senha, $banco, $porta);

        if ($conn->connect_error) {
            die("Erro na conexão: " . $conn->connect_error);
        }
        ?>
        ```

### 2. Clonar o Repositório e Configurar o Projeto

1.  **Clone o Repositório:**
    ```bash
    git clone <URL_DO_SEU_REPOSITORIO_GITHUB>
    cd <NOME_DO_SEU_REPOSITORIO>
    ```
2.  **Mova para o Servidor Web:**
    * Mova a pasta clonada (`<NOME_DO_SEU_REPOSITORIO>`) para o diretório `htdocs` do XAMPP (ou `www` do WAMP). Ex: `C:\xampp\htdocs\tuly_social`.
3.  **Instale as Dependências PHP:**
    * Abra o terminal/prompt de comando.
    * Navegue até a raiz do seu projeto dentro do `htdocs` (Ex: `cd C:\xampp\htdocs\tuly_social`).
    * Execute o Composer para instalar as dependências (Dompdf):
        ```bash
        composer install
        ```
        Isso criará a pasta `vendor/` com as bibliotecas necessárias.
4.  **Crie a Pasta de Uploads:**
    * Na raiz do seu projeto (Ex: `C:\xampp\htdocs\tuly_social`), crie uma pasta chamada `uploads`.
    * Dentro de `uploads`, crie uma subpasta chamada `perfil`.
    * Certifique-se de que ambas as pastas (`uploads/` e `uploads/perfil/`) tenham permissões de escrita para o seu servidor web (em ambiente de desenvolvimento, permissão `0777` é comum, mas `0755` é mais seguro para produção).

### 3. Acessar o Projeto

1.  Com o Apache e MySQL rodando, abra seu navegador.
2.  Vá para o endereço: `http://localhost/<NOME_DA_SUA_PASTA_NO_HTDOCS>/index.html`
    * Ex: `http://localhost/tuly_social/index.html`

### Credenciais de Teste

* **Usuário:** `admin@email.com`
* **Senha:** `123456`
* **Ou você pode realizar o cadastro na plataforma também.**

---
