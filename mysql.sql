CREATE DATABASE Gandula IF NOT EXISTS;

CREATE TABLE `horario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(220) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 9 DEFAULT CHARSET = utf8;

CREATE TABLE `postagem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUserCriador` int(11) NOT NULL,
  `idTurma` int(11) NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `turma` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUserCriador` int(11) NOT NULL,
  `idUserLider` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `cor` varchar(10) NOT NULL,
  `codigo` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 7 DEFAULT CHARSET = utf8mb4;

CREATE TABLE `turmamembros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idTurma` int(11) NOT NULL,
  `idUserMembro` int(11) NOT NULL,
  `cor` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4;

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(250) NOT NULL,
  `datanascimento` date NOT NULL,
  `tipo` varchar(40) NOT NULL,
  `foto` varchar(200) NOT NULL,
  `token` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 6 DEFAULT CHARSET = utf8mb4;