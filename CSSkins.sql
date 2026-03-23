-- Created by Redgate Data Modeler (https://datamodeler.redgate-platform.com)
-- Last modification date: 2025-10-20 19:38:12.867
-- Drop foreign keys
ALTER TABLE SkinInCollection DROP FOREIGN KEY IF EXISTS SkinInCollection_Collections;
ALTER TABLE SkinInCollection DROP FOREIGN KEY IF EXISTS SkinInCollection_Skins;
ALTER TABLE Collections DROP FOREIGN KEY IF EXISTS Collections_Cases;
ALTER TABLE Skins DROP FOREIGN KEY IF EXISTS Skins_Floats;
ALTER TABLE Skins DROP FOREIGN KEY IF EXISTS Skins_Items;
ALTER TABLE Skins DROP FOREIGN KEY IF EXISTS Skins_Rarity;
ALTER TABLE MarketValue DROP FOREIGN KEY IF EXISTS MarketValue_Skins;

-- Drop tables
DROP TABLE IF EXISTS SkinInCollection;
DROP TABLE IF EXISTS MarketValue;
DROP TABLE IF EXISTS Skins;
DROP TABLE IF EXISTS Collections;
DROP TABLE IF EXISTS Cases;
DROP TABLE IF EXISTS Rarity;
DROP TABLE IF EXISTS Items;
DROP TABLE IF EXISTS Floats;

-- tables
-- Table: CaseSkin
CREATE TABLE SkinInCollection (
    CollectionID varchar(6) NOT NULL,
    SkinID int  NOT NULL,
    CONSTRAINT SkinInCollection_pk PRIMARY KEY (CollectionID,SkinID)
) COMMENT 'Bridge entity for Collections and Skins';

-- Table: Collections
CREATE TABLE Collections (
    CollectionID varchar(6) NOT NULL,
    CollectionName varchar(99) NOT NULL,
    CaseID varchar (6) NOT NULL,
    CONSTRAINT Collections_pk PRIMARY KEY (CollectionID)
) COMMENT 'All collections + cases they come from';

-- Table: Cases
CREATE TABLE Cases (
    CaseID varchar(6) NOT NULL,
    CaseName varchar(99) NOT NULL,
    CONSTRAINT Cases_pk PRIMARY KEY (CaseID)
) COMMENT 'All cases and other ways of getting skins';

-- Table: Float
CREATE TABLE Floats (
    FloatID char(2)  NOT NULL,
    Wear_Name varchar(25)  NOT NULL,
    Float_Min decimal(3,2)  NOT NULL,
    Float_Max decimal(3,2)  NOT NULL,
    CONSTRAINT Float_pk PRIMARY KEY (FloatID)
) COMMENT 'All possible wear conditions with float values';

-- Table: Items
CREATE TABLE Items (
    ItemID varchar(4)  NOT NULL,
    Item_Name varchar(50)  NOT NULL,
    CONSTRAINT Items_pk PRIMARY KEY (ItemID)
) COMMENT 'Type of items';

-- Table: MarketValue
CREATE TABLE MarketValue (
    MValueID int AUTO_INCREMENT NOT NULL,
    SkinID int  NOT NULL,
    Price decimal(10,2)  NOT NULL,
    Date date  NOT NULL,
    CONSTRAINT MarketValue_pk PRIMARY KEY (MValueID),
    INDEX idx_skin_date (SkinID, Date)
);

-- Table: Rarity
CREATE TABLE Rarity (
    RarityID varchar(2)  NOT NULL,
    Rarity_Name varchar(99)  NOT NULL,
    Color varchar(20)  NOT NULL,
    Drop_Chance decimal(5,2)  NOT NULL,
    CONSTRAINT Rarity_pk PRIMARY KEY (RarityID)
) COMMENT 'Rarities of skins';

-- Table: Skins
CREATE TABLE Skins (
    SkinID int  NOT NULL,
    Skin_Name varchar(99)  NOT NULL,
    ItemID varchar(4)  NOT NULL,
    Pattern int  NOT NULL,
    FloatID char(2)  NOT NULL,
    RarityID varchar(2) NOT NULL,
    StatTrak boolean default FALSE,
    CONSTRAINT Skins_pk PRIMARY KEY (SkinID)
) COMMENT 'Skins that I own';

-- foreign keys
-- Reference: Collections_Cases (table: Collections)
ALTER TABLE Collections ADD CONSTRAINT Collections_Cases FOREIGN KEY (CaseID)
    REFERENCES Cases (CaseID);

-- Reference: SkinInCollection_Collections (table: SkinInCollection)
ALTER TABLE SkinInCollection ADD CONSTRAINT SkinInCollection_Collections FOREIGN KEY (CollectionID)
    REFERENCES Collections (CollectionID);

-- Reference: SkinInCollection_Skins (table: SkinInCollection)
ALTER TABLE SkinInCollection ADD CONSTRAINT SkinInCollection_Skins FOREIGN KEY (SkinID)
    REFERENCES Skins (SkinID);

-- Reference: Skins_Floats (table: Skins)
ALTER TABLE Skins ADD CONSTRAINT Skins_Floats FOREIGN KEY (FloatID)
    REFERENCES Floats (FloatID);

-- Reference: Skins_Items (table: Skins)
ALTER TABLE Skins ADD CONSTRAINT Skins_Items FOREIGN KEY (ItemID)
    REFERENCES Items (ItemID);

-- Reference: MarketValue_Skins (table: MarketValue)
ALTER TABLE MarketValue ADD CONSTRAINT MarketValue_Skins FOREIGN KEY (SkinID)
    REFERENCES Skins (SkinID);

-- Reference: Skins_Rarity (table: Skins)
ALTER TABLE Skins ADD CONSTRAINT Skins_Rarity FOREIGN KEY (RarityID)
    REFERENCES Rarity (RarityID);


-- CASES
INSERT INTO Cases VALUES ('SGT', 'Sealed Genesis Terminal');
INSERT INTO Cases VALUES ('FEVER', 'Fever Case');
INSERT INTO Cases VALUES ('GALL', 'Gallery Case');
INSERT INTO Cases VALUES ('KILO', 'Kilowatt Case');
INSERT INTO Cases VALUES ('ANUB', 'Anubis Collection Package');
INSERT INTO Cases VALUES ('REV', 'Revolution Case');
INSERT INTO Cases VALUES ('RECL', 'Recoil Case');
INSERT INTO Cases VALUES ('DNM', 'Dreams & Nightmares Case');
INSERT INTO Cases VALUES ('RIPT', 'Operation Riptide Case');
INSERT INTO Cases VALUES ('SNAKE', 'Snakebite Case');
INSERT INTO Cases VALUES ('OBF', 'Operation Broken Fang Case');
INSERT INTO Cases VALUES ('FRAC', 'Fracture Case');
INSERT INTO Cases VALUES ('PRIS2', 'Prisma 2 Case');
INSERT INTO Cases VALUES ('CS20', 'CS20 Case');
INSERT INTO Cases VALUES ('XP250', 'X-Ray P250 Package');
INSERT INTO Cases VALUES ('SWEB', 'Shattered Web Case');
INSERT INTO Cases VALUES ('PRIS', 'Prisma Case');
INSERT INTO Cases VALUES ('DZ', 'Danger Zone Case');
INSERT INTO Cases VALUES ('HZN', 'Horizon Case');
INSERT INTO Cases VALUES ('CLTCH', 'Clutch Case');
INSERT INTO Cases VALUES ('SPEC2', 'Spectrum 2 Case');
INSERT INTO Cases VALUES ('HYDRA', 'Operation Hydra Case');
INSERT INTO Cases VALUES ('SPEC', 'Spectrum Case');
INSERT INTO Cases VALUES ('GLOVE', 'Glove Case');
INSERT INTO Cases VALUES ('GAM2', 'Gamma 2 Case');
INSERT INTO Cases VALUES ('GAM', 'Gamma Case');
INSERT INTO Cases VALUES ('CHR3', 'Chroma 3 Case');
INSERT INTO Cases VALUES ('OWF', 'Operation Wildfire Case');
INSERT INTO Cases VALUES ('REVLV', 'Revolver Case');
INSERT INTO Cases VALUES ('SHAD', 'Shadow Case');
INSERT INTO Cases VALUES ('FALC', 'Falchion Case');
INSERT INTO Cases VALUES ('CHR2', 'Chroma 2 Case');
INSERT INTO Cases VALUES ('CHR', 'Chroma Case');
INSERT INTO Cases VALUES ('VAN', 'Operation Vanguard Weapon Case');
INSERT INTO Cases VALUES ('BRK', 'Operation Breakout Weapon Case');
INSERT INTO Cases VALUES ('HUNT', 'Huntsman Weapon Case');
INSERT INTO Cases VALUES ('PHX', 'Operation Phoenix Weapon Case');
INSERT INTO Cases VALUES ('WEP3', 'CS:GO Weapon Case 3');
INSERT INTO Cases VALUES ('WO', 'Winter Offensive Weapon Case');
INSERT INTO Cases VALUES ('ESW', 'eSports 2013 Winter Case');
INSERT INTO Cases VALUES ('WEP2', 'CS:GO Weapon Case 2');
INSERT INTO Cases VALUES ('BRAVO', 'Operation Bravo Case');
INSERT INTO Cases VALUES ('ES13', 'eSports 2013 Case');
INSERT INTO Cases VALUES ('WEP1', 'CS:GO Weapon Case');
INSERT INTO Cases VALUES ('WKLY', 'Weekly Drop');
INSERT INTO Cases VALUES ('ARMY', 'Armory Pass');
INSERT INTO Cases VALUES ('SVNR', 'Souvenir Package');
INSERT INTO Cases VALUES ('ES14S', 'eSports 2014 Summer Case');

-- COLLECTIONS
INSERT INTO Collections VALUES ('ASC', 'The Ascent Collection', 'WKLY');
INSERT INTO Collections VALUES ('BOREAL', 'The Boreal Collection', 'WKLY');
INSERT INTO Collections VALUES ('RAD', 'The Radiant Collection', 'WKLY');
INSERT INTO Collections VALUES ('ITLY', 'The Italy Collection', 'WKLY');
INSERT INTO Collections VALUES ('LAKE', 'The Lake Collection', 'WKLY');
INSERT INTO Collections VALUES ('BANK', 'The Bank Collection', 'WKLY');
INSERT INTO Collections VALUES ('TRN25', 'The Train 2025 Collection', 'ARMY');
INSERT INTO Collections VALUES ('FEVER', 'The Fever Collection', 'FEVER');
INSERT INTO Collections VALUES ('SPORT', 'The Sport & Field Collection', 'ARMY');
INSERT INTO Collections VALUES ('GRAPH', 'The Graphic Design Collection', 'ARMY');
INSERT INTO Collections VALUES ('LIMIT', 'Limited Edition Item', 'ARMY');
INSERT INTO Collections VALUES ('GALL', 'The Gallery Collection', 'ARMY');
INSERT INTO Collections VALUES ('OVP24', 'The Overpass 2024 Collection', 'ARMY');
INSERT INTO Collections VALUES ('KILO', 'The Kilowatt Collection', 'KILO');
INSERT INTO Collections VALUES ('ANUB', 'The Anubis Collection', 'ANUB');
INSERT INTO Collections VALUES ('REV', 'The Revolution Collection', 'REV');
INSERT INTO Collections VALUES ('RECL', 'The Recoil Collection', 'RECL');
INSERT INTO Collections VALUES ('DNM', 'The Dreams & Nightmares Collection', 'DNM');
INSERT INTO Collections VALUES ('RIPT', 'The Operation Riptide Collection', 'RIPT');
INSERT INTO Collections VALUES ('MIR21', 'The 2021 Mirage Collection', 'SVNR');
INSERT INTO Collections VALUES ('VER21', 'The 2021 Vertigo Collection', 'SVNR');
INSERT INTO Collections VALUES ('TRN21', 'The 2021 Train Collection', 'SVNR');
INSERT INTO Collections VALUES ('DU221', 'The 2021 Dust 2 Collection', 'SVNR');
INSERT INTO Collections VALUES ('SFHS', 'The Safehouse Collection', 'SVNR');
INSERT INTO Collections VALUES ('SNAKE', 'The Snakebite Collection', 'SNAKE');
INSERT INTO Collections VALUES ('OBF', 'The Operation Broken Fang Collection', 'OBF');
INSERT INTO Collections VALUES ('ANC', 'The Ancient Collection', 'SVNR');
INSERT INTO Collections VALUES ('CTRL', 'The Control Collection', 'SVNR');
INSERT INTO Collections VALUES ('HAVOC', 'The Havoc Collection', 'SVNR');
INSERT INTO Collections VALUES ('FRAC', 'The Fracture Collection', 'FRAC');
INSERT INTO Collections VALUES ('PRIS2', 'The Prisma 2 Collection', 'PRIS2');
INSERT INTO Collections VALUES ('CANAL', 'The Canals Collection', 'SVNR');
INSERT INTO Collections VALUES ('NORSE', 'The Norse Collection', 'SVNR');
INSERT INTO Collections VALUES ('STMAR', 'The St. Marc Collection', 'SVNR');
INSERT INTO Collections VALUES ('SWEB', 'The Shattered Web Collection', 'SWEB');
INSERT INTO Collections VALUES ('CS20', 'The CS20 Collection', 'CS20');
INSERT INTO Collections VALUES ('XRAY', 'The X-Ray Collection', 'XP250');
INSERT INTO Collections VALUES ('PRIS', 'The Prisma Collection', 'PRIS');
INSERT INTO Collections VALUES ('CLTCH', 'The Clutch Collection', 'CLTCH');
INSERT INTO Collections VALUES ('DZ', 'The Danger Zone Collection', 'DZ');
INSERT INTO Collections VALUES ('INF18', 'The 2018 Inferno Collection', 'SVNR');
INSERT INTO Collections VALUES ('NUK18', 'The 2018 Nuke Collection', 'SVNR');
INSERT INTO Collections VALUES ('HZN', 'The Horizon Collection', 'HZN');
INSERT INTO Collections VALUES ('SPEC2', 'The Spectrum 2 Collection', 'SPEC2');
INSERT INTO Collections VALUES ('HYDRA', 'The Operation Hydra Collection', 'HYDRA');
INSERT INTO Collections VALUES ('SPEC', 'The Spectrum Collection', 'SPEC');
INSERT INTO Collections VALUES ('GLOVE', 'The Glove Collection', 'GLOVE');
INSERT INTO Collections VALUES ('GAM2', 'The Gamma 2 Collection', 'GAM2');
INSERT INTO Collections VALUES ('GAM', 'The Gamma Collection', 'GAM');
INSERT INTO Collections VALUES ('CHR3', 'The Chroma 3 Collection', 'CHR3');
INSERT INTO Collections VALUES ('CHR2', 'The Chroma 2 Collection', 'CHR2');
INSERT INTO Collections VALUES ('CHR', 'The Chroma Collection', 'CHR');
INSERT INTO Collections VALUES ('WF', 'The Wildfire Collection', 'OWF');
INSERT INTO Collections VALUES ('REVLV', 'The Revolver Case Collection', 'REVLV');
INSERT INTO Collections VALUES ('SHAD', 'The Shadow Collection', 'SHAD');
INSERT INTO Collections VALUES ('FALC', 'The Falchion Collection', 'FALC');
INSERT INTO Collections VALUES ('VAN', 'The Vanguard Collection', 'VAN');
INSERT INTO Collections VALUES ('CACHE', 'The Cache Collection', 'SVNR');
INSERT INTO Collections VALUES ('ES14S', 'The eSports 2014 Summer Collection', 'ES14S');
INSERT INTO Collections VALUES ('COBB', 'The Cobblestone Collection', 'SVNR');
INSERT INTO Collections VALUES ('BRK', 'The Breakout Collection', 'BRK');
INSERT INTO Collections VALUES ('OVP', 'The Overpass Collection', 'SVNR');
INSERT INTO Collections VALUES ('HUNT', 'The Huntsman Collection', 'HUNT');
INSERT INTO Collections VALUES ('PHX', 'The Phoenix Collection', 'PHX');
INSERT INTO Collections VALUES ('AD3', 'The Arms Deal 3 Collection', 'WEP3');
INSERT INTO Collections VALUES ('WO', 'The Winter Offensive Collection', 'WO');
INSERT INTO Collections VALUES ('ES13W', 'The eSports 2013 Winter Collection', 'ESW');
INSERT INTO Collections VALUES ('MIR', 'The Mirage Collection', 'SVNR');
INSERT INTO Collections VALUES ('DU2', 'The Dust 2 Collection', 'SVNR');
INSERT INTO Collections VALUES ('TRN', 'The Train Collection', 'SVNR');
INSERT INTO Collections VALUES ('INF', 'The Inferno Collection', 'SVNR');
INSERT INTO Collections VALUES ('AD2', 'The Arms Deal 2 Collection', 'WEP2');
INSERT INTO Collections VALUES ('BRAVO', 'The Bravo Collection', 'BRAVO');
INSERT INTO Collections VALUES ('ES13', 'The eSports 2013 Collection', 'ES13');
INSERT INTO Collections VALUES ('AD1', 'The Arms Deal Collection', 'WEP1');
INSERT INTO Collections VALUES ('VER', 'The Vertigo Collection', 'SVNR');
INSERT INTO Collections VALUES ('NUKE', 'The Nuke Collection', 'SVNR');

-- FLOATS
INSERT INTO Floats VALUES('FN', 'Factory New', 0.00, 0.07);
INSERT INTO Floats VALUES('MW', 'Minimal Wear', 0.08, 0.15);
INSERT INTO Floats VALUES('FT', 'Field-Tested', 0.16, 0.37);
INSERT INTO Floats VALUES('WW', 'Well-Worn', 0.38, 0.44);
INSERT INTO Floats VALUES('BS', 'Battle-Scarred', 0.45, 1.00);

-- ITEM TYPES
INSERT INTO Items VALUES ('RIFL', 'Rifle');
INSERT INTO Items VALUES ('SNPR', 'Sniper Rifle');
INSERT INTO Items VALUES ('PIST', 'Pistol');
INSERT INTO Items VALUES ('SMG', 'SMG');
INSERT INTO Items VALUES ('SHTG', 'Shotgun');
INSERT INTO Items VALUES ('MCHG', 'Machinegun');
INSERT INTO Items VALUES ('KNIF', 'Knife');
INSERT INTO Items VALUES ('GLOV', 'Gloves');
INSERT INTO Items VALUES ('AGNT', 'Agent');

-- RARITIES
INSERT INTO Rarity VALUES('CG', 'Consumer Grade', 'Gray', 100);
INSERT INTO Rarity VALUES('IG', 'Industrial Grade', 'Light Blue', 100);
INSERT INTO Rarity VALUES('MS', 'Mil-Spec', 'Dark Blue', 79.95);
INSERT INTO Rarity VALUES('RS', 'Restricted', 'Purple', 15.98);
INSERT INTO Rarity VALUES('CL', 'Classified', 'Pink', 3.2);
INSERT INTO Rarity VALUES('CV', 'Covert', 'Red', 0.64);
INSERT INTO Rarity VALUES('EX', 'Extraordinary', 'Gold', 0.26);

-- End of file.