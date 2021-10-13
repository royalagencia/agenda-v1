DROP TABLE IF EXISTS `yul__agenda`;
DROP TABLE IF EXISTS `yul__app-token`;
DROP TABLE IF EXISTS `yul__code_pass`;
DROP TABLE IF EXISTS `yul__settings`;
DROP TABLE IF EXISTS `yul__users`;


CREATE TABLE `yul__agenda` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user` text NOT NULL,
  `data` datetime NOT NULL,
  `data-hora` datetime NOT NULL,
  `status` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `titulo` text NOT NULL,
  `info` text NOT NULL,
  `hash` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `yul__app-token` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(128) NOT NULL,
  `last_used` int(11) NOT NULL,
  `firebase-token` text,
  `ip` text,
  `user` varchar(64) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `yul__code_pass` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_key` text NOT NULL,
  `cod` text NOT NULL,
  `data` datetime NOT NULL,
  `data_exp` datetime NOT NULL,
  `status` text NOT NULL,
  `hash` varchar(64) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `yul__settings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `config` text,
  `valor` text,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
CREATE TABLE `yul__users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` text,
  `nome` text,
  `email` text,
  `senha` text,
  `data` datetime DEFAULT NULL,
  `hash` text,
  `status` text,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;


INSERT INTO `yul__settings` VALUES ('1','itens_menu','[{\"texto\":\"Login\",\"link\":\"/login\",\"tipo\":\"externo\"},{\"texto\":\"Dashboard\",\"link\":\"/dashboard\",\"tipo\":\"admin\"}]');
INSERT INTO `yul__settings` VALUES ('2','acesso_restrito','{\"11\":{\"DIR\":\"configuracoes\",\"acesso\":\"admin\"},\"13\":{\"DIR\":\"dashboard\",\"acesso\":\"admin\"},\"14\":{\"DIR\":\"meus-dados\",\"acesso\":\"normal\"},\"19\":{\"DIR\":\"usuarios\",\"acesso\":\"admin\"}}');
INSERT INTO `yul__settings` VALUES ('3','email_system','contato@teste.com.br');
INSERT INTO `yul__settings` VALUES ('4','social_contact','{\"facebook\":\"https://facebook.com\",\"whatsapp\":\"5524992428219\",\"twitter\":\"https://twitter.com/\",\"linkedin\":\"https://linkedin.com\",\"youtube\":\"https://www.youtube.com\"}');
INSERT INTO `yul__settings` VALUES ('5','external-smtp','false');


INSERT INTO `yul__users` VALUES ('6','admin','Administrador Master','admin@admin.com','cdf4a007e2b02a0c49fc9b7ccfbb8a10c644f635e1765dcf2a7ab794ddc7edac',NULL,'be7388724bb8cc0245ae7664a2d19b72','Ativo');
INSERT INTO `yul__users` VALUES ('7','normal','Pollyana','polly@teste.com','cdf4a007e2b02a0c49fc9b7ccfbb8a10c644f635e1765dcf2a7ab794ddc7edac','2021-10-13 10:44:20','75c59410c2bc7ef76ae51c7354f53495','Ativo');


