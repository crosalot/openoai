<?
/*
* +----------------------------------------------------------------------+
* | PHP Version 4                                                        |
* +----------------------------------------------------------------------+
* | Copyright (c) 2002-2005 Heinrich Stamerjohanns                       |
* |                                                                      |
* | identify.php -- Utilities for the OAI Data Provider                  |
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
// $Id: identify.php,v 1.02 2003/04/08 14:15:34 stamer Exp $
//

// parse and check args
if (empty($errors) && (count($args) > 0)){
	foreach ($args as $key=>$val) {
		$errors .= oai_error('badArgument', $key, $val);
	}
}

// break and clean up on error
if ($errors != '') {
	oai_exit();
}
// see http://www.openarchives.org/OAI/2.0/guidelines-oai-identifier.htm 
// for details

$indent = 2;
$output .= " <Identify>\n";
$output .= xmlformat($repositoryName, 'repositoryName', '', $indent);
$output .= xmlformat($baseURL, 'baseURL', '', $indent);
$output .= xmlformat($protocolVersion, 'protocolVersion', '', $indent);
$output .= xmlformat($adminEmail, 'adminEmail', '', $indent);
$output .= xmlformat($earliestDatestamp, 'earliestDatestamp', '', $indent);
$output .= xmlformat($deletedRecord,'deletedRecord', '', $indent);
$output .= xmlformat($granularity, 'granularity', '', $indent); 
$output .= xmlformat($compression, 'compression', '', $indent);

// A description MAY be included.
// Use this if you choose to comply with a specific format of unique identifiers
// for items. 
// See http://www.openarchives.org/OAI/2.0/guidelines-oai-identifier.htm 
// for details

if ($show_identifier && $repositoryIdentifier && $delimiter && $sampleIdentifier) {
	$output .= 
'  <description>
   <oai-identifier xmlns="http://www.openarchives.org/OAI/2.0/oai-identifier"
                   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                   xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai-identifier
                   http://www.openarchives.org/OAI/2.0/oai-identifier.xsd">
    <scheme>oai</scheme>
    <repositoryIdentifier>'.$repositoryIdentifier.'</repositoryIdentifier>
    <delimiter>'.$delimiter.'</delimiter>
    <sampleIdentifier>'.$sampleIdentifier.'</sampleIdentifier>
   </oai-identifier>
  </description>'."\n"; 
}

// A description MAY be included.
// This example from arXiv.org is used by the e-prints community, please adjust
// see http://www.openarchives.org/OAI/2.0/guidelines-eprints.htm for details

// To include, change 'false' to 'true'.
if (false) {
	$output .= 
'  <description>
   <eprints xmlns="http://www.openarchives.org/OAI/1.1/eprints"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://www.openarchives.org/OAI/1.1/eprints 
            http://www.openarchives.org/OAI/1.1/eprints.xsd">
    <content>
     <text>Author self-archived e-prints</text>
    </content>
    <metadataPolicy />
    <dataPolicy />
    <submissionPolicy />
   </eprints>
  </description>'."\n"; 
}

// If you want to point harvesters to other repositories, you can list their
// base URLs. Usage of friends container is RECOMMENDED.
// see http://www.openarchives.org/OAI/2.0/guidelines-friends.htm 
// for details

// To include, change 'false' to 'true'.
if (false) {
	$output .= 
'  <description>
   <friends xmlns="http://www.openarchives.org/OAI/2.0/friends/" 
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/friends/
            http://www.openarchives.org/OAI/2.0/friends.xsd">
    <baseURL>http://naca.larc.nasa.gov/oai2.0/</baseURL>
    <baseURL>http://techreports.larc.nasa.gov/ltrs/oai2.0/</baseURL>
    <baseURL>http://physnet.uni-oldenburg.de/oai/oai2.php</baseURL>
    <baseURL>http://cogprints.soton.ac.uk/perl/oai</baseURL>
    <baseURL>http://ub.uni-duisburg.de:8080/cgi-oai/oai.pl</baseURL>
    <baseURL>http://rocky.dlib.vt.edu/~jcdlpix/cgi-bin/OAI1.1/jcdlpix.pl</baseURL>
   </friends>
  </description>'."\n"; 
}

// If you want to provide branding information, adjust accordingly.
// Usage of friends container is OPTIONAL.
// see http://www.openarchives.org/OAI/2.0/guidelines-branding.htm 
// for details

// To include, change 'false' to 'true'.
if (false) {
	$output .= 
'  <description>
   <branding xmlns="http://www.openarchives.org/OAI/2.0/branding/"
             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/branding/
             http://www.openarchives.org/OAI/2.0/branding.xsd">
    <collectionIcon>
     <url>http://my.site/icon.png</url>
     <link>http://my.site/homepage.html</link>
     <title>MySite(tm)</title>
     <width>88</width>
     <height>31</height>
    </collectionIcon>
    <metadataRendering 
     metadataNamespace="http://www.openarchives.org/OAI/2.0/oai_dc/" 
     mimeType="text/xsl">http://some.where/DCrender.xsl</metadataRendering>
    <metadataRendering
     metadataNamespace="http://another.place/MARC" 
     mimeType="text/css">http://another.place/MARCrender.css</metadataRendering>
   </branding>
  </description>'."\n";
}

// End Identify
$output .= " </Identify>\n";

?>
