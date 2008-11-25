<?php
/**
 * Fonctions getting informations from the Openx server
 *
 */
class tx_openx_refreshCacheField {


	/**
	 * Keeps the agencies's checkbox 
	 *
	 * @param array $config
	 * @return array
	 */
	function refreshAgencies($config) {
		return $this->refreshCacheField($config,'ox_refreshcacheAgencies');
	}
	/**
	 * Keeps the zone's checkbox 
	 *
	 * @param array $config
	 * @return array
	 */
	function refreshZones($config) {
		return $this->refreshCacheField($config,'ox_refreshcacheZone');
	}
	/**
	 * Keeps the banners's checkbox 
	 *
	 * @param array $config
	 * @return array
	 */
	function refreshBanners($config) {
		return $this->refreshCacheField($config,'ox_refreshcacheBanners');
	}
	/**
	 * Keeps the campaign's checkbox 
	 *
	 * @param array $config
	 * @return array
	 */
	function refreshCampaigns($config) {
		return $this->refreshCacheField($config,'ox_refreshcacheCampaigns');
	}
	/**
	 * Keeps the advertiser's checkbox 
	 *
	 * @param array $config
	 * @return array
	 */
	function refreshAdvertisers($config) {
		return $this->refreshCacheField($config,'ox_refreshcacheAdvertisers');
	}
	
	/**
	 * Keeps the checkbox unchecked
	 *
	 * @param 	array		$config
	 * @param 	string		$fieldname
	 * @return 	array		
	 */
	function refreshCacheField($config,$fieldname) {
		if (!empty($config['row']['pi_flexform'])) {
			$flexFormContent = t3lib_div::xml2array($config['row']['pi_flexform']);
			$value = $flexFormContent['data']['ox_update']['lDEF'][$fieldname]['vDEF'];
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