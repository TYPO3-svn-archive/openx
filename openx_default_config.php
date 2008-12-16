<?php
/**
 * Openx default configuration
 * must match with the openx server config
 */
$TYPO3_CONF_VARS['EXTCONF']['openx'] = array(

	// Folders (need a slash at the beginning)
	'OpenxFolders'	=> array(
	
		'Delivery'			=>'/www/jedelivre',
		'Images'			=>'/www/mesimages'),
		
	// Files
	'OpenxDeliveryFiles' => array(
	
		'AdClick'			=>'ox-ck.php',
		'AdConversionTable'	=>'ox-tv.php',
		'AdContent'			=>'ox-ac.php',
		'AdConversion'		=>'ox-ti.php',
		'AdConversionJs'	=>'ox-tjs.php',
		'AdFrame'			=>'ox-afr.php',
		'AdImage'			=>'ox-ai.php',
		'AdJs'				=>'ox-ajs.php',
		'AdLayer'			=>'ox-al.php',
		'AdLog'				=>'ox-lg.php',
		'AdPopup'			=>'ox-apu.php',
		'AdView'			=>'ox-avw.php',
		'XMLRPC'			=>'ox-axmlrpc.php',
		'LocalInvocation'	=>'ox-alocal.php',
		'FrontController'	=>'ox-fc.php',
		'FlashInclude'		=>'ox-fl.js'
		
	)
);

?>
