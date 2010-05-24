<?
/*
* +----------------------------------------------------------------------+
* | PHP Version 4                                                        |
* +----------------------------------------------------------------------+
* | Copyright (c) 2002-2005 Heinrich Stamerjohanns                       |
* |                                                                      |
* | dc_record.php -- Utilities for the OAI Data Provider                 |
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
// $Id: record_dc.php,v 1.02 2003/04/08 14:32:07 stamer Exp $
//

// this handles unqualified DC records, but can be also used as a sample
// for other formats.
// just specify the next variable according to your own metadata prefix
// change output your metadata records further below.

// please change to the according metadata prefix you use 
$prefix = 'oai_dc';

// you do need to change anything in the namespace and schema stuff
// the correct headers should be created automatically

$output .= 
'<metadata>';

$output .= metadataHeader($prefix);

// please change according to your metadata format
$indent = 0;

$output .= xmlrecord($record['dc_title'], 'dc:title', '', $indent);
$output .= xmlrecord($record['dc_creator'],'dc:creator', '', $indent);
$output .= xmlrecord($record['dc_subject'], 'dc:subject', '', $indent);
$output .= xmlrecord($record['dc_description'], 'dc:description', '', $indent);
$output .= xmlrecord($record['dc_publisher'], 'dc:publisher', '', $indent);
$output .= xmlrecord($record['dc_contributor'], 'dc:contributor', '', $indent);
$output .= xmlrecord($record['dc_date'], 'dc:date', '', $indent);
$output .= xmlrecord($record['dc_type'], 'dc:type', '', $indent);
$output .= xmlrecord($record['dc_format'], 'dc:format', '', $indent);
$output .= xmlrecord($record['dc_identifier'], 'dc:identifier', '', $indent);
$output .= xmlrecord($record['dc_source'], 'dc:source', '', $indent);
$output .= xmlrecord($record['dc_language'], 'dc:language', '', $indent);
$output .= xmlrecord($record['dc_relation'], 'dc:relation', '', $indent);
$output .= xmlrecord($record['dc_coverage'], 'dc:coverage', '', $indent);
$output .= xmlrecord($record['dc_rights'], 'dc:rights', '', $indent);


// Here, no changes need to be done
$output .=           
'</'.$prefix;
if (isset($METADATAFORMATS[$prefix]['record_prefix'])) {
	$output .= ':'.$METADATAFORMATS[$prefix]['record_prefix'];
}
$output .= ">";
$output .= 
'</metadata>';
?>
