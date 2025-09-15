CREATE TABLE tt_content (
  tx_yw_selected_pages text,
  tx_yw_show_latest TINYINT(1) NOT NULL DEFAULT '0',
  tx_yw_latest_limit INT(11) NOT NULL DEFAULT '5'
);

CREATE TABLE IF NOT EXISTS tx_yw_bs_carousel_pages_mm (
  uid_local int(11) unsigned NOT NULL DEFAULT 0,
  uid_foreign int(11) unsigned NOT NULL DEFAULT 0,
  sorting int(11) unsigned NOT NULL DEFAULT 0,
  sorting_foreign int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (uid_local, uid_foreign)
);

