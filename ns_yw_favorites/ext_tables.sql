CREATE TABLE tx_nsywfavorites_domain_model_addtofavourite (
	name varchar(255) NOT NULL DEFAULT '',
	desc varchar(255) NOT NULL DEFAULT '',
	defaultpic varchar(255) NOT NULL DEFAULT '',
	contain varchar(255) NOT NULL DEFAULT '',
	pic int(11) unsigned NOT NULL DEFAULT '0',
	editabletoall int(11) NOT NULL DEFAULT '0',
	user varchar(255) NOT NULL DEFAULT '',
	editable varchar(255) NOT NULL DEFAULT '',
	username varchar(255) NOT NULL DEFAULT '',
);

CREATE TABLE tx_nsywfavorites_urls (
	uid int(11) NOT NULL auto_increment,
	origurl TEXT NOT NULL DEFAULT '',
	crypticurl TEXT NOT NULL DEFAULT '',
	listid int(11) NOT NULL,
	PRIMARY KEY (uid)
);

CREATE TABLE tx_nsywfavorites_domain_model_favourite (
	name varchar(255) NOT NULL DEFAULT '',
	desc varchar(255) NOT NULL DEFAULT '',
	contain varchar(255) NOT NULL DEFAULT '',
	pic int(11) unsigned NOT NULL DEFAULT '0',
	user int(11) unsigned NOT NULL DEFAULT '0'
);
