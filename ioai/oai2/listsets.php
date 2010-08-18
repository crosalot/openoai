<? 
/*
* +----------------------------------------------------------------------+
* | PHP Version 4                                                        |
* +----------------------------------------------------------------------+
* | Copyright (c) 2002-2005 Heinrich Stamerjohanns                       |
* |                                                                      |
* | listsets.php -- Utilities for the OAI Data Provider                  |
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
// $Id: listsets.php,v 1.00 2002/08/26 13:08:04 stamer Exp $
//

// parse and check arguments
foreach($args as $key => $val) {

	switch ($key) { 
		case 'resumptionToken':
			$resumptionToken = $val;
			$errors .= oai_error('badResumptionToken', $key, $val); 
			break;

		default:
			$errors .= oai_error('badArgument', $key, $val);
	}
}

// break and clean up on error
if ($errors != '') {
	oai_exit();
}

if (is_array($SETS)) {
	$output .= "<ListSets>";
	foreach($SETS as $key=>$val) {
		$output .= "<set>";
		$output .= xmlformat($val['setSpec'], 'setSpec', '', 4);
		$output .= xmlformat($val['setName'], 'setName', '', 4);
		if (isset($val['setDescription']) && $val['setDescription'] != '') {
			$output .= "<setDescription>";
			$prefix = 'oai_dc';
			$output .= metadataHeader($prefix);
			$output .= xmlrecord($val['setDescription'], 'dc:description', '', 7);
			$output .=           
			'</'.$prefix;
			if (isset($METADATAFORMATS[$prefix]['record_prefix'])) {
				$output .= ':'.$METADATAFORMATS[$prefix]['record_prefix'];
			}
			$output .= ">";
			$output .= "</setDescription>";
		}
		$output .= "</set>";
	}
	$output .= "</ListSets>"; 
}
else {
	$errors .= oai_error('noSetHierarchy'); 
	oai_exit();
}

?>
