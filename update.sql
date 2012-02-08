-- fix last entries
DROP TABLE wsif1_1_category_last_entry;
CREATE TABLE wsif1_1_category_last_entry (
	categoryID INT(10) NOT NULL DEFAULT 0,
	languageID INT(10) NOT NULL DEFAULT 0,
	entryID INT(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (categoryID, languageID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;