-- fix last entries
TRUNCATE TABLE wsif1_1_category_last_entry;
ALTER TABLE wsif1_1_category_last_entry DROP PRIMARY KEY;
ALTER TABLE wsif1_1_category_last_entry ADD PRIMARY KEY (categoryID, languageID);