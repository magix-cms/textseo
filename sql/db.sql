CREATE TABLE IF NOT EXISTS `mc_textseo` (
    `id_to` int(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    `type_to` varchar(30) DEFAULT NULL,
    `date_register` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `mc_textseo_content` (
  `id_content` int(5) NOT NULL AUTO_INCREMENT,
  `id_to` int(5) unsigned NOT NULL,
  `id_lang` smallint(3) unsigned NOT NULL,
  `content_to` text,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_content`),
  KEY `id_to` (`id_to`),
  KEY `id_lang` (`id_lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `mc_textseo_content`
  ADD CONSTRAINT `mc_textseo_content_ibfk_2` FOREIGN KEY (`id_lang`) REFERENCES `mc_lang` (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mc_textseo_content_ibfk_1` FOREIGN KEY (`id_to`) REFERENCES `mc_textseo` (`id_to`) ON DELETE CASCADE ON UPDATE CASCADE;