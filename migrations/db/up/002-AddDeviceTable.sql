CREATE TABLE IF NOT EXISTS `UserDevice` (
    `ID` INT AUTO_INCREMENT NOT NULL,
    `UserID` INT NOT NULL,
    `Identifier` NVARCHAR(256) NOT NULL,
    `LinkPhrase` NVARCHAR(35) NOT NULL,
    `Created` DATETIME NOT NULL,
    `Linked` DATETIME NULL,
    `LastActive` DATETIME NULL,
    PRIMARY KEY (`ID`)
);