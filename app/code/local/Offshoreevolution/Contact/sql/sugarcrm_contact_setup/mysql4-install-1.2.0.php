<?php
$installer = $this;
$installer->startSetup();

$prefix = Mage::getConfig()->getTablePrefix();

$installer->run('CREATE TABLE IF NOT EXISTS `oepl_sugar` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `module` varchar(20) NOT NULL,
				  `meta_key` varchar(500) NOT NULL,
				  `meta_value` varchar(500) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;');
				  
$installer->run('CREATE TABLE IF NOT EXISTS `oepl_sugar_map` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `module` varchar(100) NOT NULL,
				  `mag_id` varchar(100) NOT NULL,
				  `sugar_id` varchar(100) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');

$installer->run("CREATE TABLE IF NOT EXISTS `oepl_map_fields` (
				  `pid` int(11) NOT NULL AUTO_INCREMENT,
				  `module` varchar(150) NOT NULL,
				  `field_type` enum('text','select','radio','checkbox','textarea','filler') NOT NULL DEFAULT 'text',
				  `data_type` varchar(50) NOT NULL,
				  `field_name` varchar(255) NOT NULL,
				  `field_value` text NOT NULL,
				  `mag_field` varchar(100) NOT NULL,
				  `mag_field_type` varchar(100) NOT NULL DEFAULT 'Default',
				  `wp_meta_label` varchar(200) NOT NULL,
				  `display_order` int(11) NOT NULL,
				  `is_show` enum('Y','N') NOT NULL DEFAULT 'N',
				  `show_column` enum('1','2') NOT NULL DEFAULT '1',
				  PRIMARY KEY (`pid`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=72 ;");
				  
$installer->endSetup();
?>