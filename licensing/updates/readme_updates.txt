/*
**************************************************************************************************************************
** CORAL Licensing Module Add-on
**
** Copyright (c) 2010 University of Notre Dame
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

This Add-on will display a list of Licenses / Resources that have been modified during the timeframe specified in the configuration file.
In order to track all the changes a TRIGGER is added to the tables in the database.  Because of this not all modifications will 
appear from the beginning but over time it will become more complete.

Installation:

Make Changes to the configuration.ini
	Changes to the Licensing configuration.ini located in the admin directory.  A configuration_sample.ini is included with the Add-on.

	The following setting is REQUIRED so that the Add-on's can find the Resources database
		resourcesDatabaseName=""  	// Fill in with the correct Resources Database
	
	The following [addon] settings are OPTIONAL.  daybefore and dayafter is used by both 
		[addon]
		update_daysbefore="30"		// Number of days before the current date to show records in updates.php
		update_daysafter="90"		// Number of days after the current date to show records in updates.php
		

Execute the database_modification.sql script on your MySQL server.  Notes are in the script.

Copy Files

updates.php 

into your licensing folder.  you should then be able to access them like any other webpage.

	http://yourserver/yourcoralfolder/licensing/updates.php

	
