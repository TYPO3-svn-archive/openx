<?php
/**
 * Openx default configuration
 * must match with the openx server config
 */
$TYPO3_CONF_VARS['EXTCONF']['openx'] = array(

	// Folders (need a slash at the beginning)
	'OpenxFolders'	=> array(
	
		'Delivery'			=>'/www/delivery',
		'Images'			=>'/www/images'),
		
	// Files
	'OpenxDeliveryFiles' => array(
	
		'AdClick'			=>'ck.php',
		'AdConversionTable'	=>'tv.php',
		'AdContent'			=>'ac.php',
		'AdConversion'		=>'ti.php',
		'AdConversionJs'	=>'tjs.php',
		'AdFrame'			=>'afr.php',
		'AdImage'			=>'ai.php',
		'AdJs'				=>'ajs.php',
		'AdLayer'			=>'al.php',
		'AdLog'				=>'lg.php',
		'AdPopup'			=>'apu.php',
		'AdView'			=>'avw.php',
		'XMLRPC'			=>'axmlrpc.php',
		'LocalInvocation'	=>'alocal.php',
		'FrontController'	=>'fc.php',
		'FlashInclude'		=>'fl.php'
		
	)
);

?>
