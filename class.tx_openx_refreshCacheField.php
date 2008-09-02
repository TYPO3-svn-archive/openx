<?php
/**
 * Fonctions getting informations from the Openx server
 *
 */
class tx_openx_refreshCacheField {

	/**
	 * Keeps the checkbox unchecked
	 *
	 * @param array $config
	 * @return array
	 */
	function refreshCacheField($config) {
		if (!empty($config['row']['pi_flexform'])) {
			$flexFormContent = t3lib_div::xml2array($config['row']['pi_flexform']);
			$value = $flexFormContent['data']['ox_update']['lDEF']['ox_refreshcache']['vDEF'];
			$value == 1 ? $value = 2 : $value = 1;
		}
		$config['items'] = array(0 => array($GLOBALS['LANG']->sL('LLL:EXT:openx/locallang_db.xml:openx.pi_flexform.cacheBtn'), $value, null));
		return $config;
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/openx/class.tx_openx_refreshCacheField.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/openx/class.tx_openx_refreshCacheField.php']);
}
?>