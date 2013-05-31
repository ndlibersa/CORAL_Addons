<?php

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

include_once 'directory.php';

$pageTitle='Home';
include 'templates/header.php';

//used for creating a "sticky form" for back buttons
//except we don't want it to retain if they press the 'index' button
//check what referring script is

if (isset($_SESSION['ref_script']) && ($_SESSION['ref_script'] != "license.php")){
	$reset='Y';
}else{
	$reset='N';
}

$_SESSION['ref_script']=$currentPage;

//below includes search options in left pane only - the results are refreshed through ajax and placed in div searchResults

//print header
$pageTitle='Calendar';

$config = new Configuration;

$host = $config->database->host;
$username = $config->database->username;
$password = $config->database->password;
$license_databaseName = $config->database->name;
$resource_databaseName = $config->settings->resourcesDatabaseName;

$linkID = mysql_connect($host, $username, $password) or die("Could not connect to host.");

// Check both databases are defined.
mysql_select_db($resource_databaseName, $linkID) or die("Could not find the Resource database.");
mysql_select_db($license_databaseName, $linkID) or die("Could not find the License database.");

if ($config->addon->update_daysafter) {
	$dayafter = $config->addon->update_daysafter;
} else {
	$dayafter = "30";
}

if ($config->addon->update_daysbefore) {
	$daybefore = $config->addon->update_daysbefore;
} else {
	$daybefore = "90";

}

echo "<!-- Start minus current day $daybefore End plus current day $dayafter-->";

$query = "
SELECT 
  $license_databaseName.`License`.`licenseID` AS id,
	  IF($license_databaseName.`License`.`ts` IS NULL OR $license_databaseName.`License`.`ts` = '0000-00-00 00:00:00', 
	  $license_databaseName.`License`.`createDate`, $license_databaseName.`License`.`ts`) 
	  AS `ts`,
   'License' as type,
	$license_databaseName.`License`.`shortName` AS shortName,
	$license_databaseName.`License`.`licenseID` AS lID,
	(SELECT $license_databaseName.`License`.`shortName` FROM $license_databaseName.`License` WHERE $license_databaseName.`License`.`licenseID` = lID) licenseName 

FROM $license_databaseName.`License`
WHERE 
$license_databaseName.`License`.`ts` BETWEEN (CURDATE() - INTERVAL " . $daybefore . " DAY) AND (CURDATE() + INTERVAL " . $dayafter . " DAY) OR
$license_databaseName.`License`.`createDate` BETWEEN (CURDATE() - INTERVAL " . $daybefore . " DAY) AND (CURDATE() + INTERVAL " . $dayafter . " DAY) 

UNION Select
  $license_databaseName.`Document`.`documentID` AS id,
  $license_databaseName.`Document`.`ts` AS `ts`,
   'License:Document' as type,
	$license_databaseName.`Document`.`shortName` as shortName,
	$license_databaseName.`Document`.`licenseID` AS lID,
	(SELECT `$license_databaseName`.`License`.`shortName` FROM `$license_databaseName`.`License` WHERE `$license_databaseName`.`License`.`licenseID` = lID) licenseName
FROM
  `$license_databaseName`.`Document`
WHERE `$license_databaseName`.`Document`.`ts` BETWEEN (CURDATE() - INTERVAL " . $daybefore . " DAY) AND (CURDATE() + INTERVAL " . $dayafter . " DAY)

UNION Select
  `$license_databaseName`.`Attachment`.`attachmentID` AS `id`,
  `$license_databaseName`.`Attachment`.`ts` AS `ts`,
   'License:Attachment' as type,
	`$license_databaseName`.`Attachment`.`attachmentText` As shortName,
	`$license_databaseName`.`Attachment`.`licenseID` AS lID,
	(SELECT `$license_databaseName`.`License`.`shortName` FROM `$license_databaseName`.`License` WHERE `$license_databaseName`.`License`.`licenseID` = lID) licenseName
FROM
  `$license_databaseName`.`Attachment`
WHERE `$license_databaseName`.`Attachment`.`ts` BETWEEN (CURDATE() - INTERVAL " . $daybefore . " DAY) AND (CURDATE() + INTERVAL " . $dayafter . " DAY)

UNION SELECT 
  `$resource_databaseName`.`Resource`.`resourceID` AS id,
   IF(`$resource_databaseName`.`Resource`.`ts` IS NULL OR `$resource_databaseName`.`Resource`.`ts` = '0000-00-00 00:00:00', 
   `$resource_databaseName`.`Resource`.`createDate`, `$resource_databaseName`.`Resource`.`ts`) 
   AS `ts`,
   'Resource' as type,
	`$resource_databaseName`.`Resource`.`titleText` AS shortName,
	`$resource_databaseName`.`Resource`.`resourceID` AS lID,
	(SELECT `$resource_databaseName`.`Resource`.`titleText` FROM `$resource_databaseName`.`Resource` WHERE `$resource_databaseName`.`Resource`.`resourceID` = lID) licenseName
	
FROM `$resource_databaseName`.`Resource`
WHERE 
`$resource_databaseName`.`Resource`.`ts` BETWEEN (CURDATE() - INTERVAL " . $daybefore . " DAY) AND (CURDATE() + INTERVAL " . $dayafter . " DAY) OR
`$resource_databaseName`.`Resource`.`createDate` BETWEEN (CURDATE() - INTERVAL " . $daybefore . " DAY) AND (CURDATE() + INTERVAL " . $dayafter . " DAY)

UNION Select
  `$resource_databaseName`.`Attachment`.`attachmentID` AS `id`,
  `$resource_databaseName`.`Attachment`.`ts` AS `ts`,
   'Resource:Attachment' as type,
	`$resource_databaseName`.`Attachment`.`shortName` As shortName,
	`$resource_databaseName`.`Attachment`.`resourceID` AS lID,
	(SELECT `$resource_databaseName`.`Resource`.`titleText` FROM `$resource_databaseName`.`Resource` WHERE `$resource_databaseName`.`Resource`.`resourceID` = lID) licenseName
FROM
  `$resource_databaseName`.`Attachment`
WHERE `$resource_databaseName`.`Attachment`.`ts` BETWEEN (CURDATE() - INTERVAL " . $daybefore . " DAY) AND (CURDATE() + INTERVAL " . $dayafter . " DAY)
ORDER by ts DESC
";

$result = mysql_query($query, $linkID) or die("Query Results Failure");

?>

<div style='text-align:left;'>
	<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
		<tr style='vertical-align:top;'>
			<td>
				<b>Latest Updates</b>
			</td>
		</tr>
	</table>

	
	<div id="searchResults">
		<table style="width: 100%;" class="dataTable">
			<tbody>	
			<?php
				$mDate = "";
				$mMonth = "";
				$i = -1;
				
				while ($row = mysql_fetch_assoc($result)) {
					$i = $i + 1;
							
								
					if ($mDate != date("m/d/Y", strtotime($row["ts"]))) {
						$mDate = date("m/d/Y", strtotime($row["ts"]));
						echo "<tr>";
						echo "<th colspan='2'><table class='noBorderTable'><tbody><tr><td>" . $mDate . "</td></tr></tbody></table></th>";
						echo "</tr>";
					}	
					
					echo "<tr>";
				
					if ($i % 2 == 0) {
						$alt = "alt";
					} else {
						$alt = "";
					}
					
					echo "<td  colspan='2' class='$alt'>";
					
					if ($row["type"] == 'License') {
						echo "&nbsp;&nbsp;&nbsp;" . $row["type"] . ":  " . "<a href='license.php?licenseID=" . $row["lID"] . "'><b>". $row["licenseName"] . "</b></a>";	
					} 
					
					if ($row["type"] == 'Resource') { 
						echo "&nbsp;&nbsp;&nbsp;" . $row["type"] . ":  " . "<a href='../resources/resource.php?resourceID=" . $row["lID"] . "'><b>". $row["licenseName"] . "</b></a>";						
					}
					
					if ($row["type"] == 'License:Attachment') {
					
						$query2 = "SELECT attachmentURL FROM `$license_databaseName`.AttachmentFile WHERE attachmentID = " . $row["id"];
						$result2 = mysql_query($query2, $linkID) or die("Query Failure");					
						$row2 = mysql_fetch_assoc($result2);

						echo "&nbsp;&nbsp;&nbsp;" . str_replace(':Attachment', '', $row["type"]) . ":  " . "<a href='license.php?licenseID=" . $row["lID"] . "'><b>". $row["licenseName"] . "</b></a>";
						echo "<br>";						

						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $row["type"] . ":  ";
						echo "<a href='attachments/" . $row2["attachmentURL"] . "'>". $row["shortName"] . "</a>";
						echo " ";
					}

					if ($row["type"] == 'License:Document') {
					
						$query2 = "SELECT documentURL FROM `$license_databaseName`.Document WHERE documentID = " . $row["id"];
						$result2 = mysql_query($query2, $linkID) or die("Query Failure");					
						$row2 = mysql_fetch_assoc($result2);
					
						echo "&nbsp;&nbsp;&nbsp;" . str_replace(':Document', '', $row["type"]) . ":  " . "<a href='license.php?licenseID=" . $row["lID"] . "'><b>". $row["licenseName"] . "</b></a>";

						echo "<br>";
						
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $row["type"] . ":  ";
						echo "<a href='documents/" . $row2["documentURL"] . "'>". $row["shortName"] . "</a>";
						echo " ";
					}

					if ($row["type"] == 'Resource:Attachment') {
					
						$query2 = "SELECT attachmentURL FROM $resource_databaseName.Attachment WHERE attachmentID = " . $row["id"];
						$result2 = mysql_query($query2, $linkID) or die("Query Failure");					
						$row2 = mysql_fetch_assoc($result2);

						echo "&nbsp;&nbsp;&nbsp;" . str_replace(':Attachment', '', $row["type"]) . ":  " . "<a href='../resources/resource.php?resourceID=" . $row["lID"] . "'><b>". $row["licenseName"] . "</b></a>";

						echo "<br>";
						
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $row["type"] . ":  ";
						echo "<a href='attachments/" . $row2["attachmentURL"] . "'>". $row["shortName"] . "</a>";
						echo " ";
					}					
					
				
					echo "</td>";

					echo "</tr>";

					
				}
			?>	
			</tbody>
		</table>
	</div>	


	
</div>
<br />

<?php

  //print footer
  include 'templates/footer.php';
?>
