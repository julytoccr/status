<?php

/**
 * @link http://www.ister.org/code/simplexml44/index.xhtml  
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'IsterXmlSimpleXMLImpl.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'array2xml.php';

function simplexmlstring($string)
{
	$impl = new IsterXmlSimpleXMLImpl;
  	return $impl->load_string($string);  		
}

function simplexmlfile($file)
{
	$impl = new IsterXmlSimpleXMLImpl;
  	return $impl->load_file($file);  		
	
}


?>
