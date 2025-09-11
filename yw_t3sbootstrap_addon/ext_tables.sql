CREATE TABLE tt_content (
  tx_yw_selected_pages text
);

CREATE TABLE IF NOT EXISTS tx_yw_bs_carousel_pages_mm (
  uid_local int(11) unsigned NOT NULL DEFAULT 0,
  uid_foreign int(11) unsigned NOT NULL DEFAULT 0,
  sorting int(11) unsigned NOT NULL DEFAULT 0,
  sorting_foreign int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (uid_local, uid_foreign)
);

