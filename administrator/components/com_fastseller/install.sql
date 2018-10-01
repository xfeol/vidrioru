CREATE TABLE IF NOT EXISTS `jos_vm_product_type` (
  `product_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_type_name` varchar(255) NOT NULL DEFAULT '',
  `product_type_description` text,
  `product_type_publish` char(1) DEFAULT NULL,
  `product_type_browsepage` varchar(255) DEFAULT NULL,
  `product_type_flypage` varchar(255) DEFAULT NULL,
  `product_type_list_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`product_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `jos_vm_product_product_type_xref` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `product_type_id` int(11) NOT NULL DEFAULT '0',
  KEY `idx_product_product_type_xref_product_id` (`product_id`),
  KEY `idx_product_product_type_xref_product_type_id` (`product_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Maps products to a product type';

CREATE TABLE IF NOT EXISTS `jos_vm_product_type_parameter` (
  `product_type_id` int(11) NOT NULL DEFAULT '0',
  `parameter_name` varchar(255) NOT NULL DEFAULT '',
  `parameter_label` varchar(255) NOT NULL DEFAULT '',
  `parameter_description` text,
  `parameter_list_order` int(11) NOT NULL DEFAULT '0',
  `parameter_type` char(1) NOT NULL DEFAULT 'T',
  `parameter_values` varchar(255) DEFAULT NULL,
  `parameter_multiselect` char(1) DEFAULT NULL,
  `parameter_default` varchar(255) DEFAULT NULL,
  `parameter_unit` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`product_type_id`,`parameter_name`),
  KEY `idx_product_type_parameter_parameter_order` (`parameter_list_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Parameters which are part of a product type';