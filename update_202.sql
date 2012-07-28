-- category structure
ALTER TABLE wsif1_1_category ADD showOrder INT(10) NOT NULL DEFAULT 0;

UPDATE	wsif1_1_category category
SET	showOrder = (
		SELECT	position
		FROM	wsif1_1_category_structure
		WHERE	categoryID = category.categoryID
		LIMIT	1
	);

DROP TABLE IF EXISTS wsif1_1_category_structure;

-- entry menu items
DROP TABLE IF EXISTS wsif1_1_entry_menu_item;

-- entry ratings
DROP TABLE IF EXISTS wsif1_1_entry_rating;

-- entry subscriptions
DROP TABLE IF EXISTS wsif1_1_entry_subscription;
CREATE TABLE wsif1_1_entry_subscription (
	userID INT(10) NOT NULL DEFAULT 0,
	entryID INT(10) NOT NULL DEFAULT 0,
	PRIMARY KEY (userID, entryID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;