<?php
/**
 * Fonctions getting informations from the Openx server
 *
 */
class tx_openx_xmlrpc_getinfos {

	protected $xmlRpcHost 		= "";
	protected $webXmlRpcDir 	= "";
	protected $username 		= "";
	protected $password 		= "";

	protected $agencyId			= 1;

	protected $service			= false;
	protected $noMoreCacheCheck	= false;

	function __construct() {
	}

	/**
	 * List all agencies defined in the Openx server
	 *
	 * @param  array $config
	 * @return array
	 */
	function listAgencies($config) {
		$this->checkCaches($config['row']['uid']);
		if (!is_file(PATH_site.'typo3temp/openx/ox_cache_Agencies.php')) $this->createCaches("Agencies");
		include(PATH_site.'typo3temp/openx/ox_cache_Agencies.php');
		$items = unserialize($Agencies);
		
		foreach ($items as $agencie) {
			$config['items'][] = $agencie;
		}

		return $config;
	}

	/**
	 * List all zones defined in the Openx server
	 *
	 * @param  array $config
	 * @return array
	 */
	function listZones($config) {
		$this->checkCaches($config['row']['uid']);
		if (!is_file(PATH_site.'typo3temp/openx/ox_cache_Zones.php')) $this->createCaches("Zones");
		include(PATH_site.'typo3temp/openx/ox_cache_Zones.php');
		$items = unserialize($Zones);

		foreach ($items as $zone) {
			$config['items'][] = $zone;
		}

		return $config;
	}

	/**
	 * List all campaigns defined in the Openx server
	 *
	 * @param  array $config
	 * @return array
	 */
	function listCampaigns($config) {
		$this->checkCaches($config['row']['uid']);
		if (!is_file(PATH_site.'typo3temp/openx/ox_cache_Campaigns.php')) $this->createCaches("Campaigns");
		include(PATH_site.'typo3temp/openx/ox_cache_Campaigns.php');
		$items = unserialize($Campaigns);
		
		foreach ($items as $campaign) {
			$config['items'][] = $campaign;
		}

		return $config;
	}

	/**
	 * List all banners defined in the Openx server
	 *
	 * @param  array $config
	 * @return array
	 */
	function listBanners($config) {
		$this->checkCaches($config['row']['uid']);
		if (!is_file(PATH_site.'typo3temp/openx/ox_cache_Banners.php')) $this->createCaches("Banners");
		include(PATH_site.'typo3temp/openx/ox_cache_Banners.php');
		$items = unserialize($Banners);
		
		foreach ($items as $banner) {
			$config['items'][] = $banner;
		}

		return $config;
	}

	/**
	 * List all advertisers defined in the Openx server
	 *
	 * @param  array $config
	 * @return array
	 */
	function listAdvertisers($config) {
		$this->checkCaches($config['row']['uid']);
		if (!is_file(PATH_site.'typo3temp/openx/ox_cache_Advertisers.php')) $this->createCaches("Advertisers");
		include(PATH_site.'typo3temp/openx/ox_cache_Advertisers.php');
		$items = unserialize($Advertisers);
		
		foreach ($items as $advertiser) {
			$config['items'][] = $advertiser;
		}

		return $config;
	}
	
	/**
	 * Create a new cache for every Openx informations
	 *
	 * @param integer $uid
	 */
	function checkCaches($uid) {
		if (!$this->noMoreCacheAgencies && $GLOBALS['_POST']['data']['tt_content'][$uid]['pi_flexform']['data']['ox_update']['lDEF']['ox_refreshcacheAgencies']['vDEF']) {
			$this->createCaches("Agencies");
			$this->noMoreCacheAgencies = true;
		}
		if (!$this->noMoreCacheZone && $GLOBALS['_POST']['data']['tt_content'][$uid]['pi_flexform']['data']['ox_update']['lDEF']['ox_refreshcacheZone']['vDEF']) {
			$this->createCaches("Zones");
			$this->noMoreCacheZone = true;
		}
		if (!$this->noMoreCacheBanners && $GLOBALS['_POST']['data']['tt_content'][$uid]['pi_flexform']['data']['ox_update']['lDEF']['ox_refreshcacheBanners']['vDEF']) {
			$this->createCaches("Banners");
			$this->noMoreCacheBanners = true;
		}
		if (!$this->noMoreCacheCampaigns && $GLOBALS['_POST']['data']['tt_content'][$uid]['pi_flexform']['data']['ox_update']['lDEF']['ox_refreshcacheCampaigns']['vDEF']) {
			$this->createCaches("Campaigns");
			$this->noMoreCacheCampaigns = true;
		}
		if (!$this->noMoreCacheAdvertisers && $GLOBALS['_POST']['data']['tt_content'][$uid]['pi_flexform']['data']['ox_update']['lDEF']['ox_refreshcacheAdvertisers']['vDEF']) {
			$this->createCaches("Advertisers");
			$this->noMoreCacheAdvertisers = true;
		}
	}

	/**
	 * Create caches files
	 *
	 * @param string $infos	Info's type we want to generate (default, all), can be 'Agencies', 'Advertisers', 'Banners', 'Campaigns' or 'Zones'
	 */
	function createCaches($infos = 'all')
	{

		if (!@include_once('api/openads-api-xmlrpc.inc.php')) {
			die('Error: cannot load the OpenAds XML_RPC proxy class');
		}
		// load Extension constants		
		$extConf 		= unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['openx']);
		$xmlRpcHost 		= $extConf['OpenxServerDomain'];
		$webXmlRpcDir 		= "/".$extConf['OpenxRootFolder'].'/www/api/v1/xmlrpc';
		$username 		= $extConf['OpenxUsername'];
		$password 		= $extConf['OpenxPassword'];

		$this->service = new tx_OpenxApiXmlrpc($xmlRpcHost, $webXmlRpcDir, $username, $password);
		$this->publisher = $this->service->getPublisher($extConf['OpenxpublisherId']);
		$this->agencyId = $this->publisher->agencyId;
		
		
		if ($infos=='all') $caches = array('Agencies', 'Advertisers', 'Banners', 'Campaigns', 'Zones');
		else $caches = array($infos);
		
		foreach ($caches as $type) {
			$datas = false;
			switch ($type) {
				case 'Agencies':
					$datas = serialize($this->getAgencies());
					break;
				case 'Advertisers':
					$datas = serialize($this->getAdvertisers());
					break;
				case 'Banners':
					$datas = serialize($this->getBanners());
					break;
				case 'Campaigns':
					$datas = serialize($this->getCampaigns());
					break;
				case 'Zones':
					$datas = serialize($this->getZones());
					break;
			}
			if ($datas) {
				$content = "<?php $".$type." = '".$datas."'; ?>";
				$cachefilename = t3lib_div::getFileAbsFileName('typo3temp/openx/ox_cache_'.$type.'.php');
				t3lib_div::writeFileToTypo3tempDir($cachefilename,$content);
			}
		}
	}
	
	/**
	 * Get all zones defined in the Openx server
	 *
	 * @param  array $config
	 * @return array
	 */
	function getAgencies() {
		
		$items = array();

		$agencyList = $this->service->getAgencyList();

		foreach ($agencyList as $agencie) {
			$items[] = array(0 => $agencie->agencyName, 1 => $agencie->agencyId);
		}

		return $items;
	}

	/**
	 * Get all zones defined in the Openx server
	 *
	 * @param  array $config
	 * @return array
	 */
	function getZones() {
		
		$items = array();

		$zoneList = $this->service->getZoneListByPublisherId($this->publisher->publisherId);

		foreach ($zoneList as $zones) {
			$items[] = array(0 => $zones->zoneName, 1 => $zones->zoneId);
		}

		return $items;
	}

	/**
	 * Get all campaigns defined in the Openx server
	 *
	 * @param  array $config
	 * @return array
	 */
	function getCampaigns() {

		$items = array();

		// Get every advertisers on this website
		$advertiserList = $this->service->getAdvertiserListByAgencyId($this->agencyId);
		foreach ($advertiserList as $advertiser) {

			// Get every campaigns on this website
			$campaignList = $this->service->getCampaignListByAdvertiserId($advertiser->advertiserId);
			foreach ($campaignList as $campaign) {
				$items[] = array(0 => $advertiser->advertiserName.' >> '.$campaign->campaignName, 1 => $campaign->campaignId);
			}
		}

		return $items;
	}

	/**
	 * Get all banners defined in the Openx server
	 *
	 * @param  array $config
	 * @return array
	 */
	function getBanners() {

		$items = array();

		$advertiserList = $this->service->getAdvertiserListByAgencyId($this->agencyId);
		foreach ($advertiserList as $advertiser) {

			// Get every campaigns on this website
			$campaignList = $this->service->getCampaignListByAdvertiserId($advertiser->advertiserId);
			foreach ($campaignList as $campaign) {

				// Get every campaigns on this website
				$bannerList = $this->service->getBannerListByCampaignId($campaign->campaignId);
				foreach ($bannerList as $banner) {

					$items[] = array(0 => $advertiser->advertiserName.' >> '.$campaign->campaignName.' >> '.$banner->bannerName, 1 => $banner->bannerId);
				}
			}
		}
		return $items;
	}

	/**
	 * Get all advertisers defined in the Openx server
	 *
	 * @param  array $config
	 * @return array
	 */
	function getAdvertisers() {

		$items = array();

		$advertiserList = $this->service->getAdvertiserListByAgencyId($this->agencyId);
		foreach ($advertiserList as $advertiser) {

			// Get every campaigns on this website
			$campaignList = $this->service->getCampaignListByAdvertiserId($advertiser->advertiserId);
			foreach ($campaignList as $campaign) {

				$items[] = array(0 => $advertiser->advertiserName.' >> '.$campaign->campaignName, 1 => $campaign->campaignId);
			}
		}

		return $items;
	}
	
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/openx/class.tx_openx_xmlrpc_getinfos.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/openx/class.tx_openx_xmlrpc_getinfos.php']);
}
?>