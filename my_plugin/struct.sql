CREATE TABLE /*TABLE_PREFIX*/t_plugin_table_one (
	pk_i_id INT NOT NULL AUTO_INCREMENT,
	s_name VARCHAR(60) NULL,
	i_num INT NULL,
	dt_pub_date DATETIME NOT NULL,
	dt_date DATETIME NOT NULL,
	s_url TEXT NOT NULL,
	b_active BOOLEAN NOT NULL DEFAULT FALSE,

	PRIMARY KEY (pk_i_id)
)	ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_plugin_table_two (
	pk_i_id INT NOT NULL AUTO_INCREMENT,
	fk_i_one_id INT NULL,

	PRIMARY KEY (pk_i_id),
	INDEX (fk_i_one_id),
	FOREIGN KEY (fk_i_one_id) REFERENCES /*TABLE_PREFIX*/t_plugin_table_one (pk_i_id)
)	ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';