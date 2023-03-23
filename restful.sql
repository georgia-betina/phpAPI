-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23-Mar-2023 às 19:21
-- Versão do servidor: 10.4.27-MariaDB
-- versão do PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `restful`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido`
--

CREATE TABLE `pedido` (
  `codigo` int(11) NOT NULL,
  `data` datetime DEFAULT NULL,
  `total` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pedido`
--

INSERT INTO `pedido` (`codigo`, `data`, `total`) VALUES
(0, '2023-02-23 15:10:20', 155),
(44, '2023-03-23 13:59:29', 206.78),
(45, '2023-03-23 15:20:26', 6499);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produto`
--

CREATE TABLE `produto` (
  `codigo` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL DEFAULT '',
  `valor` float NOT NULL DEFAULT 0,
  `tipo` int(11) NOT NULL DEFAULT 0,
  `ativado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produto`
--

INSERT INTO `produto` (`codigo`, `nome`, `valor`, `tipo`, `ativado`) VALUES
(1, 'Pão', 10, 1, 1),
(2, 'Picanha', 90, 1, 1),
(3, 'Maionese', 3.99, 1, 1),
(4, 'Celular xing-ling', 2000, 2, 1),
(6, 'Mel', 25, 1, 1),
(8, 'Saco de lixo', 2, 7, 1),
(9, 'Móveis', 15, 1, 0),
(10, 'Pizza', 20, 1, 0),
(11, 'Maçã', 3.5, 1, 0),
(12, 'Banana', 2.99, 1, 0),
(13, 'Cadeira', 350.99, 9, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produto_pedido`
--

CREATE TABLE `produto_pedido` (
  `codigo` int(11) NOT NULL,
  `pedido` int(11) DEFAULT NULL,
  `produto` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `total` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produto_pedido`
--

INSERT INTO `produto_pedido` (`codigo`, `pedido`, `produto`, `quantidade`, `total`) VALUES
(1, 0, 2, 2, 50),
(2, 0, 3, 1, 5),
(3, 0, 1, 20, 100),
(38, 44, 2, 2, 180),
(39, 44, 3, 2, 7.98),
(40, 45, 2, 1, 90),
(41, 45, 4, 2, 4000);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipo_produto`
--

CREATE TABLE `tipo_produto` (
  `codigo` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `percentual_imposto` float NOT NULL DEFAULT 0,
  `ativado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tipo_produto`
--

INSERT INTO `tipo_produto` (`codigo`, `nome`, `percentual_imposto`, `ativado`) VALUES
(1, 'Alimento', 10, 1),
(2, 'Eletrônico', 60, 1),
(4, 'Papelaria', 2, 1),
(5, 'Sapatos', 10, 1),
(6, 'G', 10, 1),
(7, 'Limpeza', 15, 0),
(8, 'Vestuário', 45, 0),
(9, 'Móveis', 25, 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `fktipo` (`tipo`);

--
-- Índices para tabela `produto_pedido`
--
ALTER TABLE `produto_pedido`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `fkpedido` (`pedido`),
  ADD KEY `fkproduto` (`produto`);

--
-- Índices para tabela `tipo_produto`
--
ALTER TABLE `tipo_produto`
  ADD PRIMARY KEY (`codigo`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `produto_pedido`
--
ALTER TABLE `produto_pedido`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `tipo_produto`
--
ALTER TABLE `tipo_produto`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `produto`
--
ALTER TABLE `produto`
  ADD CONSTRAINT `fktipo` FOREIGN KEY (`tipo`) REFERENCES `tipo_produto` (`codigo`);

--
-- Limitadores para a tabela `produto_pedido`
--
ALTER TABLE `produto_pedido`
  ADD CONSTRAINT `fkpedido` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`codigo`),
  ADD CONSTRAINT `fkproduto` FOREIGN KEY (`produto`) REFERENCES `produto` (`codigo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
