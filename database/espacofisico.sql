-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 07/04/2025 às 04:02
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
(1, 'Maracanã/Francisco Negrão de Lima', 'Maracanã');

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
(4, 1, 'Auditório 31', 100),
(5, 1, 'Auditório 33', 100),
(6, 1, 'Auditório 51', 100),
(7, 1, 'Auditório 71', 100),
(8, 1, 'Auditório 91', 100),
(9, 1, 'Auditório 93', 100),
(10, 1, 'Auditório 111', 100),
(11, 1, 'Auditório 113', 100);

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
-- Estrutura para tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `id_solicitante` int(11) NOT NULL,
  `id_unidade_solicitante` int(11) NOT NULL,
  `id_responsavel` int(11) DEFAULT NULL,
  `nome_responsavel` varchar(255) DEFAULT NULL,
  `id_unidade_responsavel` int(11) DEFAULT NULL,
  `nome_unidade_responsavel` varchar(255) DEFAULT NULL,
  `email_responsavel` varchar(255) DEFAULT NULL,
  `telefone1_responsavel` varchar(20) DEFAULT NULL,
  `telefone2_responsavel` varchar(20) DEFAULT NULL,
  `id_aprovador` int(11) NOT NULL,
  `id_unidade_aprovador` int(11) NOT NULL,
  `email_aprovador` varchar(255) DEFAULT NULL,
  `telefone1_aprovador` varchar(20) DEFAULT NULL,
  `telefone2_aprovador` varchar(20) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `quantidade_participantes` int(11) NOT NULL,
  `assinado_solicitante` tinyint(1) NOT NULL DEFAULT 0,
  `assinado_componente_org` tinyint(1) NOT NULL DEFAULT 0,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento_espaco_data_hora`
--

CREATE TABLE `evento_espaco_data_hora` (
  `id` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `id_predio` int(11) DEFAULT NULL,
  `id_espaco` int(11) DEFAULT NULL,
  `data_hora_inicio` datetime NOT NULL,
  `data_hora_fim` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento_recursos`
--

CREATE TABLE `evento_recursos` (
  `id` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `id_recurso` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento_status`
--

CREATE TABLE `evento_status` (
  `id` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `id_status` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento_verificacao`
--

CREATE TABLE `evento_verificacao` (
  `id` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `codigo_verificador` varchar(50) NOT NULL,
  `codigo_crc` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
(2, 1, 'Capela Ecumênica', 'Capela'),
(3, 1, 'Bosque', 'Bosque'),
(4, 1, 'Espinha da Baleia', 'Baleia');

-- --------------------------------------------------------

--
-- Estrutura para tabela `recursos`
--

CREATE TABLE `recursos` (
  `id` int(11) NOT NULL,
  `id_espaco` int(11) DEFAULT NULL,
  `id_predio` int(11) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `tipo` enum('Audiovisual','Mobiliário') NOT NULL,
  `status` enum('disponivel','em manutencao','indisponivel') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `recursos`
--

INSERT INTO `recursos` (`id`, `id_espaco`, `id_predio`, `nome`, `quantidade`, `tipo`, `status`) VALUES
(5, NULL, NULL, 'Poltrona', 2, 'Mobiliário', 'disponivel'),
(6, 1, NULL, 'Microfone', 12, 'Audiovisual', 'disponivel'),
(7, NULL, 3, 'Refletor', 1, 'Audiovisual', 'disponivel'),
(8, NULL, 2, 'Pedestal', 1, 'Audiovisual', ''),
(9, 4, NULL, 'Filmagem', 1, 'Audiovisual', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `status_definicao`
--

CREATE TABLE `status_definicao` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `ordem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `status_definicao`
--

INSERT INTO `status_definicao` (`id`, `nome`, `descricao`, `ordem`) VALUES
(1, 'Início', 'Assinatura aprovador pendente', 1),
(2, 'Solicitado', 'Assinado pelo aprovador', 2),
(3, 'Recebido', 'Em análise', 3),
(4, 'Agendado', 'Confirmado', 4),
(5, 'Recusado', 'Explicar motivo', 5),
(6, 'Cancelado', 'Solicitação cancelada', 6);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `expira_em` datetime NOT NULL,
  `tipo` varchar(50) NOT NULL
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
-- Índices de tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `evento_espaco_data_hora`
--
ALTER TABLE `evento_espaco_data_hora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_evento` (`id_evento`),
  ADD KEY `id_espaco` (`id_espaco`),
  ADD KEY `evento_espaco_data_hora_ibfk_3` (`id_predio`);

--
-- Índices de tabela `evento_recursos`
--
ALTER TABLE `evento_recursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_evento` (`id_evento`),
  ADD KEY `fk_recurso` (`id_recurso`);

--
-- Índices de tabela `evento_status`
--
ALTER TABLE `evento_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_evento` (`id_evento`),
  ADD KEY `idx_status` (`id_status`);

--
-- Índices de tabela `evento_verificacao`
--
ALTER TABLE `evento_verificacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_evento_verificacao_eventos` (`id_evento`);

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
  ADD KEY `id_espaco` (`id_espaco`),
  ADD KEY `fk_recursos_predio` (`id_predio`);

--
-- Índices de tabela `status_definicao`
--
ALTER TABLE `status_definicao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_token` (`token`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `espaco_fotos`
--
ALTER TABLE `espaco_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=243;

--
-- AUTO_INCREMENT de tabela `evento_espaco_data_hora`
--
ALTER TABLE `evento_espaco_data_hora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT de tabela `evento_recursos`
--
ALTER TABLE `evento_recursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de tabela `evento_status`
--
ALTER TABLE `evento_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `evento_verificacao`
--
ALTER TABLE `evento_verificacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `predio`
--
ALTER TABLE `predio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `recursos`
--
ALTER TABLE `recursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `status_definicao`
--
ALTER TABLE `status_definicao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  ADD CONSTRAINT `evento_espaco_data_hora_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_espaco_data_hora_ibfk_2` FOREIGN KEY (`id_espaco`) REFERENCES `espacos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evento_espaco_data_hora_ibfk_3` FOREIGN KEY (`id_predio`) REFERENCES `predio` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `evento_recursos`
--
ALTER TABLE `evento_recursos`
  ADD CONSTRAINT `fk_evento` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recurso` FOREIGN KEY (`id_recurso`) REFERENCES `recursos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `evento_status`
--
ALTER TABLE `evento_status`
  ADD CONSTRAINT `evento_status_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evento_status_ibfk_2` FOREIGN KEY (`id_status`) REFERENCES `status_definicao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `evento_verificacao`
--
ALTER TABLE `evento_verificacao`
  ADD CONSTRAINT `fk_evento_verificacao_eventos` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `predio`
--
ALTER TABLE `predio`
  ADD CONSTRAINT `predio_ibfk_1` FOREIGN KEY (`id_campus`) REFERENCES `campus` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `recursos`
--
ALTER TABLE `recursos`
  ADD CONSTRAINT `fk_recursos_predio` FOREIGN KEY (`id_predio`) REFERENCES `predio` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recursos_ibfk_1` FOREIGN KEY (`id_espaco`) REFERENCES `espacos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
