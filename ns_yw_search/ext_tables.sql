#
# Table structure for table 'sys_category'
#
CREATE TABLE sys_category (
	images int(11) unsigned default '0'
);

#
# Table structure for table 'tx_kesearch_filteroptions'
#
CREATE TABLE tx_kesearch_filteroptions (
	isParent int(11) unsigned default '0',
	image varchar(255) DEFAULT NULL,
	category_image int(11) unsigned default '0'
);
