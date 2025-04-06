-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/04/2025 às 05:34
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

--
-- Despejando dados para a tabela `eventos`
--

INSERT INTO `eventos` (`id`, `id_solicitante`, `id_unidade_solicitante`, `id_responsavel`, `nome_responsavel`, `id_unidade_responsavel`, `nome_unidade_responsavel`, `email_responsavel`, `telefone1_responsavel`, `telefone2_responsavel`, `id_aprovador`, `id_unidade_aprovador`, `email_aprovador`, `telefone1_aprovador`, `telefone2_aprovador`, `nome`, `quantidade_participantes`, `assinado_solicitante`, `assinado_componente_org`, `observacoes`, `created_at`, `updated_at`) VALUES
(208, 46, 20, 0, 'sdafsdaf', 0, 'sadfsadfsad', 'sadfsadf', '(12) 21122-112', '', 46, 20, 'diogo.nascimento@uerj.br', '(21) 98710-5175', '(21) 98935-0698', 'Evento Teste 1', 42, 0, 0, 'Campo destinado a observações\r\n                                ', '2025-04-05 20:58:41', '2025-04-05 21:25:50');

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

--
-- Despejando dados para a tabela `evento_espaco_data_hora`
--

INSERT INTO `evento_espaco_data_hora` (`id`, `id_evento`, `id_espaco`, `data_hora_inicio`, `data_hora_fim`) VALUES
(181, 208, 1, '2025-04-04 10:30:00', '2025-04-04 16:30:00');

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

--
-- Despejando dados para a tabela `evento_recursos`
--

INSERT INTO `evento_recursos` (`id`, `id_evento`, `id_recurso`, `quantidade`) VALUES
(35, 208, 5, 1),
(36, 208, 6, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento_status`
--

CREATE TABLE `evento_status` (
  `id` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `observacoes` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `evento_status`
--

INSERT INTO `evento_status` (`id`, `id_evento`, `status`, `id_usuario`, `observacoes`, `created_at`, `updated_at`) VALUES
(124, 208, 'assinatura pendente', NULL, NULL, '2025-04-05 15:38:16', '2025-04-05 15:38:16'),
(127, 208, 'solicitacao assinada pelo aprovador', 46, NULL, '2025-04-06 03:33:17', '2025-04-06 03:33:17');

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
-- Despejando dados para a tabela `tokens`
--

INSERT INTO `tokens` (`id`, `id_usuario`, `token`, `criado_em`, `expira_em`, `tipo`) VALUES
(2, 46, '208.14078bcecddfbd5b22649370c3d175ba', '2025-04-05 18:38:16', '9999-12-31 23:59:59', 'aprovacao');

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
  ADD KEY `id_espaco` (`id_espaco`);

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
  ADD KEY `id_espaco` (`id_espaco`),
  ADD KEY `fk_recursos_predio` (`id_predio`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;

--
-- AUTO_INCREMENT de tabela `evento_espaco_data_hora`
--
ALTER TABLE `evento_espaco_data_hora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT de tabela `evento_recursos`
--
ALTER TABLE `evento_recursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `evento_status`
--
ALTER TABLE `evento_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

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
-- AUTO_INCREMENT de tabela `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  ADD CONSTRAINT `evento_espaco_data_hora_ibfk_2` FOREIGN KEY (`id_espaco`) REFERENCES `espacos` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `evento_status_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
