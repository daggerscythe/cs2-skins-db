-- CSSkins_Drop.sql
-- Drop all tables in the CS2 Skins database

-- Drop foreign keys first to avoid constraint errors
ALTER TABLE SkinInCollection DROP FOREIGN KEY IF EXISTS SkinInCollection_Collections;
ALTER TABLE SkinInCollection DROP FOREIGN KEY IF EXISTS SkinInCollection_Skins;
ALTER TABLE Collections DROP FOREIGN KEY IF EXISTS Collections_Cases;
ALTER TABLE Skins DROP FOREIGN KEY IF EXISTS Skins_Floats;
ALTER TABLE Skins DROP FOREIGN KEY IF EXISTS Skins_Items;
ALTER TABLE Skins DROP FOREIGN KEY IF EXISTS Skins_Rarity;
ALTER TABLE MarketValue DROP FOREIGN KEY IF EXISTS MarketValue_Skins;

-- Drop tables in reverse order of dependencies
DROP TABLE IF EXISTS SkinInCollection;
DROP TABLE IF EXISTS MarketValue;
DROP TABLE IF EXISTS Skins;
DROP TABLE IF EXISTS Collections;
DROP TABLE IF EXISTS Cases;
DROP TABLE IF EXISTS Rarity;
DROP TABLE IF EXISTS Items;
DROP TABLE IF EXISTS Floats;
