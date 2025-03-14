-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/03/2025 às 03:39
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `espacofisico`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `campus`
--

CREATE TABLE `campus` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `sigla` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `campus`
--

INSERT INTO `campus` (`id`, `nome`, `sigla`) VALUES
(1, 'Maracanã/Francisco Negrão de Lima', 'FNL');

-- --------------------------------------------------------

--
-- Estrutura para tabela `espacos`
--

CREATE TABLE `espacos` (
  `id` int(11) NOT NULL,
  `id_predio` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `capacidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `espacos`
--

INSERT INTO `espacos` (`id`, `id_predio`, `nome`, `capacidade`) VALUES
(1, 1, 'Auditório 11', 100),
(2, 2, 'Capela', 50);

-- --------------------------------------------------------

--
-- Estrutura para tabela `espaco_fotos`
--

CREATE TABLE `espaco_fotos` (
  `id` int(11) NOT NULL,
  `id_espaco` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento`
--

CREATE TABLE `evento` (
  `id` int(11) NOT NULL,
  `id_solicitante` int(11) NOT NULL,
  `nome_solicitante` varchar(255) NOT NULL,
  `id_responsavel` int(11) NOT NULL,
  `telefone_responsavel` varchar(20) DEFAULT NULL,
  `email_responsavel` varchar(255) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `quantidade_participantes` int(11) NOT NULL,
  `assinado_solicitante` tinyint(1) NOT NULL DEFAULT 0,
  `assinado_componente_org` tinyint(1) NOT NULL DEFAULT 0,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento_espaco_data_hora`
--

CREATE TABLE `evento_espaco_data_hora` (
  `id` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `id_espaco` int(11) NOT NULL,
  `data_hora_inicio` datetime NOT NULL,
  `data_hora_fim` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento_recursos`
--

CREATE TABLE `evento_recursos` (
  `id` int(11) NOT NULL,
  `id_espaco_recurso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento_status`
--

CREATE TABLE `evento_status` (
  `id` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `predio`
--

CREATE TABLE `predio` (
  `id` int(11) NOT NULL,
  `id_campus` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `sigla` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `predio`
--

INSERT INTO `predio` (`id`, `id_campus`, `nome`, `sigla`) VALUES
(1, 1, 'Pavilhão João Lyra Filho', 'PJLF'),
(2, 1, 'Campus', 'C');

-- --------------------------------------------------------

--
-- Estrutura para tabela `recursos`
--

CREATE TABLE `recursos` (
  `id` int(11) NOT NULL,
  `id_espaco` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `tipo` enum('Audiovisual','Mobiliário') NOT NULL,
  `status` enum('disponivel','em manutencao','indisponivel') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `campus`
--
ALTER TABLE `campus`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `espacos`
--
ALTER TABLE `espacos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_predio` (`id_predio`);

--
-- Índices de tabela `espaco_fotos`
--
ALTER TABLE `espaco_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_espaco` (`id_espaco`);

--
-- Índices de tabela `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `evento_espaco_data_hora`
--
ALTER TABLE `evento_espaco_data_hora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_evento` (`id_evento`),
  ADD KEY `id_espaco` (`id_espaco`);

--
-- Índices de tabela `evento_recursos`
--
ALTER TABLE `evento_recursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_espaco_recurso` (`id_espaco_recurso`);

--
-- Índices de tabela `evento_status`
--
ALTER TABLE `evento_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_evento` (`id_evento`);

--
-- Índices de tabela `predio`
--
ALTER TABLE `predio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_campus` (`id_campus`);

--
-- Índices de tabela `recursos`
--
ALTER TABLE `recursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_espaco` (`id_espaco`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `campus`
--
ALTER TABLE `campus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `espacos`
--
ALTER TABLE `espacos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `espaco_fotos`
--
ALTER TABLE `espaco_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `evento`
--
ALTER TABLE `evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `evento_espaco_data_hora`
--
ALTER TABLE `evento_espaco_data_hora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `evento_recursos`
--
ALTER TABLE `evento_recursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `evento_status`
--
ALTER TABLE `evento_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `predio`
--
ALTER TABLE `predio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `recursos`
--
ALTER TABLE `recursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `espacos`
--
ALTER TABLE `espacos`
  ADD CONSTRAINT `espacos_ibfk_1` FOREIGN KEY (`id_predio`) REFERENCES `predio` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `espaco_fotos`
--
ALTER TABLE `espaco_fotos`
  ADD CONSTRAINT `espaco_fotos_ibfk_1` FOREIGN KEY (`id_espaco`) REFERENCES `espacos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `evento_espaco_data_hora`
--
ALTER TABLE `evento_espaco_data_hora`
  ADD CONSTRAINT `evento_espaco_data_hora_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `evento` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_espaco_data_hora_ibfk_2` FOREIGN KEY (`id_espaco`) REFERENCES `espacos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `evento_recursos`
--
ALTER TABLE `evento_recursos`
  ADD CONSTRAINT `evento_recursos_ibfk_1` FOREIGN KEY (`id_espaco_recurso`) REFERENCES `recursos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `evento_status`
--
ALTER TABLE `evento_status`
  ADD CONSTRAINT `evento_status_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `evento` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `predio`
--
ALTER TABLE `predio`
  ADD CONSTRAINT `predio_ibfk_1` FOREIGN KEY (`id_campus`) REFERENCES `campus` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `recursos`
--
ALTER TABLE `recursos`
  ADD CONSTRAINT `recursos_ibfk_1` FOREIGN KEY (`id_espaco`) REFERENCES `espacos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
