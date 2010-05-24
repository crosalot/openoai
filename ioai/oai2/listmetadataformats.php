<?
/*
* +----------------------------------------------------------------------+
* | PHP Version 4                                                        |
* +----------------------------------------------------------------------+
* | Copyright (c) 2002-2005 Heinrich Stamerjohanns                       |
* |                                                                      |
* | listmetadataformats.php -- Utilities for the OAI Data Provider       |
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
* |            stamer@uni-oldenburg.de                                   |
* +----------------------------------------------------------------------+
*/
//
// $Id: listmetadataformats.php,v 1.00 2002/08/26 13:14:33 stamer Exp $
//


// parse and check arguments
foreach($args as $key => $val) {

	switch ($key) { 
		case 'identifier':
			$identifier = $val; 
			break;

		case 'metadataPrefix':
		// only to be compatible with VT explorer
			if (is_array($METADATAFORMATS[$val])
					&& isset($METADATAFORMATS[$val]['myhandler'])) {
				$metadataPrefix = $val;
				$inc_record  = $METADATAFORMATS[$val]['myhandler'];
			} else {
				$errors .= oai_error('cannotDisseminateFormat', $key, $val);
			}
			break;

		default:
			$errors .= oai_error('badArgument', $key, $val);
	}
}

if (isset($args['identifier'])) {
	// remove the OAI part to get the identifier
	$id = str_replace($oaiprefix, '', $identifier); 

	$query = idQuery($id);
	$res = db_query($query);

	if (empty($res)) {
		$errors .= oai_error('idDoesNotExist', 'identifier', $identifier);
	}    
}

//break and clean up on error
if ($errors != '') {
	oai_exit();
}

// currently it is assumed that an existing identifier
// can be served in all available metadataformats...
// 
if (is_array($METADATAFORMATS)) {
	$output .= " <ListMetadataFormats>\n";
	foreach($METADATAFORMATS as $key=>$val) {
		$output .= "  <metadataFormat>\n";
		$output .= xmlformat($key, 'metadataPrefix', '', 3);
		$output .= xmlformat($val['schema'], 'schema', '', 3);
		$output .= xmlformat($val['metadataNamespace'], 'metadataNamespace', '', 3);
		$output .= "  </metadataFormat>\n";
	}
	$output .= " </ListMetadataFormats>\n"; 
}
else {
	$errors .= oai_error('noMetadataFormats'); 
	oai_exit();
}

?>
