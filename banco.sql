CREATE DATABASE rede_social;
USE rede_social;

-- Tabela de usuários
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL,
  senha VARCHAR(255) NOT NULL
);

-- Tabela de postagens
CREATE TABLE postagens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  conteudo TEXT NOT NULL,
  data_postagem TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Usuário de teste
INSERT INTO usuarios (email, senha) VALUES ('admin@email.com', SHA1('123456'));

CREATE TABLE IF NOT EXISTS postagens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  conteudo TEXT NOT NULL,
  imagem_url VARCHAR(255) NULL, -- Nova coluna para armazenar o caminho da imagem
  data_postagem TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

ALTER TABLE usuarios ADD COLUMN foto_perfil_url VARCHAR(255) NULL;
