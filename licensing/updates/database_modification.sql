-- This file contains the queries that need to be run on the CORAL databases
-- The Licensing and Resources database will be effected
--

-- Add a ts (timestamp) field to various tables in the Licensing database.  
-- For this statement to run properly repalce the licensing_db with the correct Licensing Database.
-- This way we will know when any Attachement, Document, or License has been modified.
--
-- The ON UPDATE will not work with CORAL code see note below.  Leaving it defined for future.
--

-- Replace licensing_db with your Licence database name
-- Replace resources_db with your Resource database name

ALTER TABLE `licensing_db`.`Attachment` ADD COLUMN `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `licensing_db`.`Document` ADD COLUMN `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `licensing_db`.`License` ADD COLUMN `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add a ts (timestamp) field to various tables in the Resources database.  
-- We are adding timestamps to the tables for an easy way to tell when a Licnese, Resource, Attachment, Document has been modified.
-- For this statement to run properly repalce the resources_db with the correct Resources Database.
-- This way we will know when any Resource or Attachment has been modified.
--
-- The ON UPDATE will not work with CORAL code see note below.  Leaving it defined for future.
--

ALTER TABLE `resources_db`.`Resource` ADD COLUMN `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `resources_db`.`Attachment` ADD COLUMN `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Because CORAL loops through all record rows upon UPDATE a trigger will need to be created.  A 
-- TIMESTAMP will not work on UPDATE since the value in the ts filed will just be updated with the previous value
-- in order for a TIMESTAMP to work the UPDATE statement must not have a value for the row that is a TIMESTAMP.
-- The CORAL update statement is in the DatabaseObject.php public function save().  
-- I do not want to modify the main CORAL branch so using TRIGGERS instead.
-- In order to create TRIGGERS you must have TRIGGER or SUPER rights and if Binary Loggin is ON you must
-- be an admin.

delimiter $$
CREATE TRIGGER `licensing_db`.attachment_timestamp 
BEFORE UPDATE ON `licensing_db`.Attachment 
FOR EACH ROW 
BEGIN
SET NEW.ts = NOW();
END$$
delimiter ;

delimiter $$
CREATE TRIGGER `licensing_db`.document_timestamp 
BEFORE UPDATE ON `licensing_db`.Document 
FOR EACH ROW 
BEGIN
SET NEW.ts = NOW();
END$$
delimiter ;

delimiter $$
CREATE TRIGGER `licensing_db`.license_timestamp 
BEFORE UPDATE ON `licensing_db`.License 
FOR EACH ROW 
BEGIN
SET NEW.ts = NOW();
END$$
delimiter ;

delimiter $$
CREATE TRIGGER `resources_db`.resource_timestamp 
BEFORE UPDATE ON `resources_db`.Resource 
FOR EACH ROW 
BEGIN
SET NEW.ts = NOW();
END$$
delimiter ;

delimiter $$
CREATE TRIGGER `resources_db`.attachment_timestamp 
BEFORE UPDATE ON `resources_db`.Attachment  
FOR EACH ROW 
BEGIN
SET NEW.ts = NOW();
END$$
delimiter ;