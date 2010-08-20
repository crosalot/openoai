<?
/*
* +----------------------------------------------------------------------+
* | PHP Version 4                                                        |
* +----------------------------------------------------------------------+
* | Copyright (c) 2002-2005 Heinrich Stamerjohanns                       |
* |                                                                      |
* | listrecords.php -- Utilities for the OAI Data Provider               |
* |                                                                      |
* | This is free software; you can redistribute it and/or modify it under|
* | the terms of the GNU General Public License as published by the      |
* | Free Software Foundation; either version 2 of the License, or (at    |
* | your option) any later version.                                      |
* | This software is distributed in the hope that it will be useful, but |
* | WITHOUT  ANY WARRANTY; without even the implied warranty of          |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the         |
* | GNU General Public License for more details.                         |
* | You should have received a copy of the GNU General Public License    |
* | along with  software; if not, write to the Free Software Foundation, |
* | Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA         |
* |                                                                      |
* +----------------------------------------------------------------------+
* | Derived from work by U. Müller, HUB Berlin, 2002                     |
* |                                                                      |
* | Written by Heinrich Stamerjohanns, May 2002                          |
* /            stamer@uni-oldenburg.de                                   |
* +----------------------------------------------------------------------+
*/
//
// $Id: listrecords.php,v 1.03 2004/07/02 14:24:21 stamer Exp $
//

// parse and check arguments
foreach($args as $key => $val) {

	switch ($key) { 
		case 'from':
			// prevent multiple from
			if (!isset($from)) {
				$from = $val;
			} else {
				$errors .= oai_error('badArgument', $key, $val);
			}
			break;

		case 'until':
			// prevent multiple until
			if (!isset($until)) {
				$until = $val; 
			} else {
				$errors .= oai_error('badArgument', $key, $val);
			}
			break;

		case 'metadataPrefix':
			if (is_array($METADATAFORMATS[$val])
					&& isset($METADATAFORMATS[$val]['myhandler'])) {
				$metadataPrefix = $val;
				$inc_record  = $METADATAFORMATS[$val]['myhandler'];
			} else {
				$errors .= oai_error('cannotDisseminateFormat', $key, $val);
			}
			break;

		case 'set':
			if (!isset($set)) {
				$set = $val;
			} else {
				$errors .= oai_error('badArgument', $key, $val);
			}
			break;      

		case 'resumptionToken':
			if (!isset($resumptionToken)) {
				$resumptionToken = $val;
			} else {
				$errors .= oai_error('badArgument', $key, $val);
			}
			
			break;

		default:
			$errors .= oai_error('badArgument', $key, $val);
	}
}


// Resume previous session?
if (isset($args['resumptionToken'])) { 		
	if (count($args) > 1) {
		// overwrite all other errors
		$errors = oai_error('exclusiveArgument');
	} else {
		if ($filetext = ioai_get_token($resumptionToken)) {
			$textparts = explode('#', $filetext);
			$deliveredrecords = (int)$textparts[0];
			$extquery = $textparts[1];
			$metadataPrefix = $textparts[2];
			
			if (is_array($METADATAFORMATS[$metadataPrefix])
					&& isset($METADATAFORMATS[$metadataPrefix]['myhandler'])) {
				$inc_record  = $METADATAFORMATS[$metadataPrefix]['myhandler'];
			} else {
				$errors .= oai_error('cannotDisseminateFormat', $key, $metadataPrefix);
			}
			
		} else { 
			$errors .= oai_error('badResumptionToken', '', $resumptionToken); 
		}
	}
}
// no, we start a new session
else {
	$deliveredrecords = 0; 
	if (!$args['metadataPrefix']) {
		$errors .= oai_error('missingArgument', 'metadataPrefix');
	}

	$extquery = '';

	if (isset($args['from'])) {
		if (!checkDateFormat($from)) {
			$errors .= oai_error('badGranularity', 'from', $from); 
		}
		$extquery .= fromQuery($from);
	}

	if (isset($args['until'])) {
		if (!checkDateFormat($until)) {
			$errors .= oai_error('badGranularity', 'until', $until);
		}
		$extquery .= untilQuery($until);
	}

    if (isset($args['set'])) {
	    if (is_array($SETS)) {
		    $extquery .= setQuery($set);
	    } else {
			$errors .= oai_error('noSetHierarchy'); 
			oai_exit();
		}
	}
}

if (empty($errors)) {
	$query = selectallQuery() . $extquery. " ORDER BY ".$SQL['datestamp'];
	
	$num_rows = ioai_num_rows($query);
  
	$maxrec = min($num_rows - $deliveredrecords, $MAXRECORDS);
	
	$query .= ' LIMIT '.(string)$deliveredrecords.', '.	$maxrec;
	
	$res = db_query($query);   
	if (!$num_rows) {
		$errors .= oai_error('noRecordsMatch');
	}
}

// break and clean up on error
if ($errors != '') {
	oai_exit();
}

ioai_delete_token($resumptionToken);

$output .= "<ListRecords>";

// Will we need a ResumptionToken?
if ($num_rows - $deliveredrecords > $MAXRECORDS) {
	$thendeliveredrecords = (int)$deliveredrecords + $MAXRECORDS;
	$token = ioai_create_token("$thendeliveredrecords#$extquery#$metadataPrefix");
	
	$restoken = '<resumptionToken expirationDate="'.$expirationdatetime.'" completeListSize="'.$num_rows.'" cursor="'.$deliveredrecords.'">'.$token."</resumptionToken>"; 
}
// Last delivery, return empty ResumptionToken
elseif (isset($args['resumptionToken'])) {
	$restoken = '<resumptionToken completeListSize="'.$num_rows.'" cursor="'.$deliveredrecords.'"></resumptionToken>';
}

// return records
$countrec  = 0;
while ($countrec++ < $maxrec) {
	// the second condition is due to a bug in PEAR
	$record = db_fetch_array($res); 

	$identifier = $shortprefix.$record[$SQL['type']].'/'.$record[$SQL['identifier']]; 
	$datestamp = formatDatestamp($record[$SQL['datestamp']]);
	 
	if (isset($record[$SQL['deleted']]) && ($record[$SQL['deleted']] == 0) &&
		($deletedRecord == 'transient' || $deletedRecord == 'persistent')) {
		$status_deleted = TRUE;
	} else {
		$status_deleted = FALSE;
	}

        $type = $record[$SQL['type']];
        $id = $record[$SQL['identifier']];

        if ($type == 'node') {
                $record = node_load($id);
        }
        elseif ($type == 'user') {
                $record = user_load($id);
                ioai_build_user($record);
        }
        elseif ($type == 'comment') {
                $record = _comment_load($id);
                ioai_build_comment($record);
        }


	//$record = node_load($record[$SQL['identifier']]);
	
	$record = $record->oai;

	$output .= '<record>';
	$output .=  '<header';
	if ($status_deleted) {
		$output .= ' status="deleted"';
	}  
	$output .='>';
	$output .= xmlformat($identifier, 'identifier', '', 4);
	$output .= xmlformat($datestamp, 'datestamp', '', 4);
	if (!$status_deleted) 
		// use xmlrecord since we use stuff from database
		$output .= xmlrecord($record[$SQL['set']], 'setSpec', '', 4);

	$output .= '</header>'; 

// return the metadata record itself
	if (!$status_deleted) {
		include($inc_record);
	}

	$output .= '</record>';   
}

// ResumptionToken
if (isset($restoken)) {
	$output .= $restoken;
}

// end ListRecords
$output .= 
'</ListRecords>';
  
?>
