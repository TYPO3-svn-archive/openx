<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

require_once(t3lib_extMgm::extPath($_EXTKEY).'openx_default_config.php');
t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_openx_pi1.php','_pi1','list_type',0);
?>