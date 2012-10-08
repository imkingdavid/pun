# Table: 'phpbb_user_notes'
CREATE TABLE phpbb_user_notes (
	note_id mediumint(8) UNSIGNED NOT NULL auto_increment,
	user_id mediumint(8) UNSIGNED NOT NULL,
	note_title varchar(255) DEFAULT '' NOT NULL,
	note_slug varchar(255) DEFAULT '' NOT NULL,
	note_contents text NOT NULL,
	allow_smilies INT(1) DEFAULT 1 NOT NULL,
	allow_bbcode INT(1) DEFAULT 1 NOT NULL,
	bbcode_uid varchar(255) DEFAULT '' NOT NULL,
	bbcode_bitfield varchar(255) DEFAULT '' NOT NULL,
	note_created_time varchar(255) DEFAULT '' NOT NULL,
	note_edited_time varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (note_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;
