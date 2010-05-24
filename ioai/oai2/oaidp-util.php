<?    
/*
* +----------------------------------------------------------------------+
* | PHP Version 4                                                        |
* +----------------------------------------------------------------------+
* | Copyright (c) 2002-2005 Heinrich Stamerjohanns                       |
* |                                                                      |
* | oaidp-util.php -- Utilities for the OAI Data Provider                |
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
// $Id: oaidp-util.php,v 1.03 2003/04/08 14:40:23 stamer Exp $
//

function get_token()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((int)($usec*1000*1000) + (int)($sec*1000*1000));
}

function oai_error($code, $argument = '', $value = '')
{
	global $request;
	global $request_err;

	switch ($code) {
		case 'badArgument' :
			$text = "The argument $argument (value = $value ) included in the request is not valid.";
			break;

		case 'badGranularity' :
			$text = "The value $value of the argument $argument is not valid.";
			$code = 'badArgument';
			break;

		case 'badResumptionToken' :
			$text = "The resumptionToken $value does not exist or has already expired.";
			break;

		case 'badRequestMethod' :
			$text = "The request method $argument is unknown.";
			$code = 'badVerb';
			break;

		case 'badVerb' :
			$text = "The verb $argument provided in the request is illegal.";
			break;

		case 'cannotDisseminateFormat' :
			$text = "The metadata format $value given by $argument is not supported by this repository.";
			break;

		case 'exclusiveArgument' :
			$text = 'The usage of resumptionToken as an argument allows no other arguments.';
			$code = 'badArgument';
			break;

		case 'idDoesNotExist' :
			$text = "The value $value of the identifier is illegal for this repository.";
			if (!is_valid_uri($value)) {
				$code = 'badArgument';
			}
			break;

		case 'missingArgument' :
			$text = "The required argument $argument is missing in the request.";
			$code = 'badArgument';
			break;

		case 'noRecordsMatch' :
			$text = 'The combination of the given values results in an empty list.';
			break;

		case 'noMetadataFormats' :
			$text = 'There are no metadata formats available for the specified item.';
			break;

		case 'noVerb' :
			$text = 'The request does not provide any verb.';
			$code = 'badVerb';
			break;

		case 'noSetHierarchy' :
			$text = 'This repository does not support sets.';
			break;

		case 'sameArgument' :
			$text = 'Do not use them same argument more than once.';
			$code = 'badArgument';
			break;

		case 'sameVerb' :
			$text = 'Do not use verb more than once.';
			$code = 'badVerb';
			break;

		default:
			$text = "Unknown error: code: $code, argument: $argument, value: $value";
			$code = 'badArgument';
	}

	if ($code == 'badVerb' || $code == 'badArgument') {
		$request = $request_err;
	}
	$error = '<error code="'.xmlstr($code, 'iso8859-1', false).'">'.xmlstr($text, 'iso8859-1', false)."</error>";
	return $error;
}

function xmlstr($string, $charset = 'iso8859-1', $xmlescaped = 'false')
{
	$xmlstr = stripslashes(trim($string));
	// just remove invalid characters
	$pattern ="/[\x-\x8\xb-\xc\xe-\x1f]/";
    $xmlstr = preg_replace($pattern, '', $xmlstr);

	// escape only if string is not escaped
	if (!$xmlescaped) {
		$xmlstr = htmlspecialchars($xmlstr, ENT_QUOTES);
	}

	if ($charset != "utf-8") {
		$xmlstr = utf8_encode($xmlstr);
	}

	return $xmlstr;
}

// will split a string into elements and return XML
// supposed to print values from database
function xmlrecord($sqlrecords, $element, $attr = '', $indent = 0, $safe = TRUE)
{
  $indent = 0;
	global $SQL;
	global $xmlescaped;
	global $charset;

	$str = '';

	if ($attr != '') {
		$attr = ' '.$attr;
	}
	
	if (!is_array($sqlrecords)) {
	  $sqlrecords = array($sqlrecords);
	}
	
	foreach ($sqlrecords as $sqlrecord) {
	  if (!empty($sqlrecord)) {
		  if (isset($SQL['split']) && !$safe) {
			  $temparr = explode($SQL['split'], $sqlrecord);
			  foreach ($temparr as $val) {
				  $str .= str_pad('', $indent).'<'.$element.$attr.'>'.xmlstr($val, $charset, $xmlescaped).'</'.$element.">";
			  }
			  $str .= $str;
		  } else {
			  $str.= str_pad('', $indent).'<'.$element.$attr.'>'.xmlstr($sqlrecord, $charset, $xmlescaped).'</'.$element.">";
		  }
	  }
	}
	
	return $str;
}

function xmlelement($element, $attr = '', &$indent, $open = true)
{
  $indent = 0;
	global $SQL;

	if ($attr != '') {
		$attr = ' '.$attr;
	}
	if ($open) {
		$indent += 2;
		return str_pad('', $indent).'<'.$element.$attr.'>';
	} else {
		$indent -= 2;
		return str_pad('', $indent).'</'.$element.'>';
	}
}

// takes either an array or a string and outputs them as XML entities
function xmlformat($record, $element, $attr = '', $indent = 0)
{
  $indent = 0;
	global $charset;
	global $xmlescaped;
		
	if ($attr != '') {
		$attr = ' '.$attr;
	}
	
	$str = '';
	if (is_array($record)) {
		foreach  ($record as $val) {
			$str .= str_pad('', $indent).'<'.$element.$attr.'>'.xmlstr($val, $charset, $xmlescaped).'</'.$element.">";
		}
		return $str;
	} elseif ($record != '') {
		return str_pad('', $indent).'<'.$element.$attr.'>'.xmlstr($record, $charset, $xmlescaped).'</'.$element.">";
	} else {
		return '';
	}
}

function date2UTCdatestamp($date)
{
	global $granularity;

	if ($date == '') return '';
	
	switch ($granularity) {

		case 'YYYY-MM-DDThh:mm:ssZ':
			// we assume common date ("YYYY-MM-DD") 
			// or datetime format ("YYYY-MM-DD hh:mm:ss")
			// or datetime format with timezone YYYY-MM-DD hh:mm:ss+02
			// or datetime format with GMT timezone YYYY-MM-DD hh:mm:ssZ
			// or datetime format with timezone YYYY-MM-DDThh:mm:ssZ
			// or datetime format with microseconds and
			//             with timezone YYYY-MM-DD hh:mm:ss.xxx+02
			// with all variations as above
			// in the database
			// 
			if (strstr($date, ' ') || strstr($date, 'T')) {
				$checkstr = '/([0-9]{4})(-)([0-9]{1,2})(-)([0-9]{1,2})([T ])([0-9]{2})(:)([0-9]{2})(:)([0-9]{2})(\.?)(\d*)([Z+-]{0,1})([0-9]{0,2})$/';
				$val = preg_match($checkstr, $date, $matches);
				if (!$val) {
					// show that we have an error
					return "0000-00-00T00:00:00Z";
				}
				// date is datetime format
				/*
				 * $matches for "2005-05-26 09:30:51.123+02"
				 *	[0] => 2005-05-26 09:30:51+02
				 *	[1] => 2005
				 *	[2] => -
				 *	[3] => 05
				 *	[4] => -
				 *	[5] => 26
				 *	[6] =>
				 *	[7] => 09
				 *	[8] => :
				 *	[9] => 30
				 *	[10] => :
				 *	[11] => 51
				 *	[12] => .
				 *	[13] => 123
				 *	[14] => +
				 *	[15] => 02
				 */
				if ($matches[14] == '+' || $matches[14] == '-') {
					// timezone is given
					// format ("YYYY-MM-DD hh:mm:ss+01")
					$tz = $matches[15];
					if ($tz != '') {
						//$timestamp = mktime($h, $min, $sec, $m, $d, $y);
						$timestamp = mktime($matches[7], $matches[9], $matches[11],
											$matches[3], $matches[5], $matches[1]);
						// add, subtract timezone offset to get GMT
						// 3600 sec = 1 h
						if ($matches[14] == '-') {
							// we are before GMT, thus we need to add
							$timestamp += (int) $tz * 3600; 
						} else {
							// we are after GMT, thus we need to subtract
							$timestamp -= (int) $tz * 3600; 
						}							
						return strftime("%Y-%m-%dT%H:%M:%SZ", $timestamp);
					}
				} elseif ($matches[14] == 'Z') {
					return str_replace(' ', 'T', $date);
				}				
				return str_replace(' ', 'T', $date).'Z';
			} else {
				// date is date format
				// granularity 'YYYY-MM-DD' should be used...
				return $date.'T00:00:00Z';
			}
			break;

		case 'YYYY-MM-DD':
			if (strstr($date, ' ')) {
				// date is datetime format
				list($date, $time) = explode(" ", $date);
				return $date;
			} else {
				return $date;
			}
			break;

		default: die("Unknown granularity!");
	}
}

function checkDateFormat($date) {

	global $granularity;
	global $message;

    if ($granularity == 'YYYY-MM-DDThh:mm:ssZ') {
		$checkstr = '([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})T([0-9]{2}):([0-9]{2}):([0-9]{2})Z$';
	} else {
		$checkstr = '([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}$)';
	}
	if (ereg($checkstr, $date, $regs)) {
		if (checkdate($regs[2], $regs[3], $regs[1])) {	
			return 1;
		}
		else {
			$message = "Invalid Date: $date is not a valid date.";
			return 0;
		}
    }
    else {
	    $message = "Invalid Date Format: $date does not comply to the date format $granularity.";
	    return 0;
    }
}

function formatDatestamp($datestamp)
{
	return strftime('%Y-%m-%dT%H:%M:%SZ', $datestamp);
}

function oai_close()
{
	global $compress;

	echo "</OAI-PMH>";

	if ($compress) {
		ob_end_flush();
	}
}

function oai_exit()
{
	global $CONTENT_TYPE;
	global $xmlheader;
	global $request;
	global $errors;
  
	header($CONTENT_TYPE);
	echo $xmlheader;
	echo $request;
	echo $errors;

	oai_close();
	exit();
}

function php_is_at_least($version) {

	list($c_r, $c_mj, $c_mn) = explode('.', phpversion());
    list($v_r, $v_mj, $v_mn) = explode('.', $version);

	if ($c_r >= $v_r && $c_mj >= $v_mj && $c_mn >= $v_mn) return TRUE;
	else return FALSE;
}

function is_valid_uri($url)
{
	return((bool)preg_match("'^[^:]+:(?://)?(?:[a-z_0-9-]+[\.]{1})*(?:[a-z_0-9-]+\.)[a-z]{2,3}.*$'i", $url));
}

function metadataHeader($prefix)
{

	global $METADATAFORMATS;
	global $XMLSCHEMA;

	$myformat = $METADATAFORMATS[$prefix];

	$str = 
	'<'.$prefix;
	if ($myformat['record_prefix']) {
		$str .= ':'.$myformat['record_prefix'];
	}
	$str .= ' xmlns:'.$prefix.'="'.$myformat['metadataNamespace'].'"';
	if ($myformat['record_prefix'] && $myformat['record_namespace']) {
		$str .= ' xmlns:'.$myformat['record_prefix'].'="'.$myformat['record_namespace'].'"';
	}
	$str .= 
	' xmlns:xsi="'.$XMLSCHEMA.'"'.' xsi:schemaLocation="'.$myformat['metadataNamespace'].$myformat['schema'].'">';

	return $str;
}
?>
