<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$GLOBALS['TCA']['tt_content']['ctrl']['requestUpdate'] .= ',ox_refreshcache';
/*$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';*/
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages';

// Activate the display of the plug-in flexform field and set FlexForm defintion

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:openx/flexform.xml');


t3lib_extMgm::addPlugin(array('LLL:EXT:openx/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

require_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_openx_xmlrpc_getinfos.php');
require_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_openx_refreshCacheField.php');
// Define the path to the static TS files

t3lib_extMgm::addStaticFile($_EXTKEY, 'static/', 'OpenX Invocation Code');


if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModule('txopenxM1','','',t3lib_extmgm::extPath($_EXTKEY).'mod/dummy/');
	t3lib_extMgm::addModule('txopenxM1','txopenxbackendM1','',t3lib_extMgm::extPath($_EXTKEY).'mod/openxbackend/');
	
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_openx_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_openx_pi1_wizicon.php';
}
?>