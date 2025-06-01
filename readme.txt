trocar as configurações de banco e cria-lo com o codigo abaixo

CREATE DATABASE IF NOT EXISTS meutrator;
USE meutrator;

-- Tabela principal para os produtos cadastrados
CREATE TABLE cadastro_produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    quantidade INT NOT NULL,
    cidadeTrator VARCHAR(100) NOT NULL,
    estadoTrator VARCHAR(100) NOT NULL,
    nuvem TEXT NULL,
    url_buy VARCHAR(255) NOT NULL,
    destacar TINYINT(1) DEFAULT 0
);

-- Tabela para armazenar imagens dos produtos
CREATE TABLE imagens_produto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    FOREIGN KEY (produto_id) REFERENCES cadastro_produto(id) ON DELETE CASCADE
);
