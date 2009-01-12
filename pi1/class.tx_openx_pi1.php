<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Cobweb <support@cobweb.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Openx banner invocation' for the 'openx' extension.
 *
 * @author	Roberto Presedo <rpresedo@cobweb.ch>
 * @package	TYPO3
 * @subpackage	tx_openx
 */
class tx_openx_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_openx_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_openx_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'openx';	// The extension key.

	// Result of the query, formated
	public $result		= false;

	// OpenX required vars
	protected $OpenxVars	= array(
	'invocation'			=>'js',		// Invocation's type
	'zone'				=>1,		// Zone id
	'campaign'			=>0,		// Campaign id
	'banner'			=>0,		// Banner id
	'target'			=>'',		// Link's target
	'withtext'			=>'',		// Additionnal text
	'charset'			=>'',		// Charset
	'block'				=>false,	// Do not show this banner on this page anymore
	'blockCampaign'			=>false,	// Do not show a banner of this campaign on this page anymore
	'clientid'			=>false,	// Client id *for JS invocation*
	'what'				=>false,	// Shortcut *for JS invocation*
	'source'			=>false,	// Source *for JS invocation*
	'cb'				=>false,	// Random number to revent cache *for JS invocation*
	'refresh'			=>'',		// # seconds before refreshing the zone *for iframe invocation*
	'frameheight'			=>'',		// iFrame's height
	'framewidth'			=>'',		// iFrame's width
	'resize'			=>false,	// Allow iFrame resizing
	'transparent'			=>false		// Makes iFrame transparent
	);

	// Params array
	protected $OpenxParams = array();

	// Openx server domain
	protected $OpenxServerDomain = '';

	// Openx Installation folder from rootpath (with a slash '/' at the beginning)
	protected $OpenxRootFolder = '/';

	// Openx folders (see openx_default_config.php)
	protected $OpenxFolders	= array();

	// Delivery's Filenames (see openx_default_config.php)
	protected $OpenxDeliveryFiles = array();

	// Openx server's urls
	protected $OpenxServerUrls = array();

	// Misc vars
	protected $OpenxMacros = array();
	protected $OpenxOpenxBackupImage = '';
	protected $uniqueid = 0;
	protected $fixIEBug = 0;

	/**
	 * Send query to the openx and return results
	 *
	 */
	function main($content, $conf) {

		// We inherit the zone info from the TS
		$conf['zone'] = $this->cObj->stdWrap($conf['zone'], $conf['zone.']);
		// Get local config
		$this->init($conf);
		// Put local config in a temporary array
		$this->temp_conf = $this->conf;
		$ret.= "<!--//\n ".print_r($this->temp_conf,1)."\n-->";

		// Openx Extension Configuration ...
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['openx']);
		$this->OpenxServerDomain	= $extConf['OpenxServerDomain'];
		$this->OpenxRootFolder		= $extConf['OpenxRootFolder'];
		// Openx Extension Default configuration (see openx_default_config.php)
		$this->OpenxDeliveryFiles 	= $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['openx']['OpenxDeliveryFiles'];
		$this->OpenxFolders 		= $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['openx']['OpenxFolders'];

		foreach ($this->OpenxVars as $key => $value) {
			if($this->temp_conf[$key]) $this->OpenxVars[$key] = $this->temp_conf[$key];
		}

		// Building URLS
		$this->OpenxServerUrls = array(
		'DeliveryEngineSSL'	=>'https://'.$this->OpenxServerDomain."/".$this->OpenxRootFolder.$this->OpenxFolders['Delivery'],
		'DeliveryEngine'	=>'http://'. $this->OpenxServerDomain."/".$this->OpenxRootFolder.$this->OpenxFolders['Delivery'],
		'ImageStoreSSL'		=>'https://'.$this->OpenxServerDomain."/".$this->OpenxRootFolder.$this->OpenxFolders['Images'],
		'ImageStore'		=>'http://'. $this->OpenxServerDomain."/".$this->OpenxRootFolder.$this->OpenxFolders['Images']);
		
		// Creates the invocation code
		switch ($this->OpenxVars['invocation']) {
			case 'local':
				$ret = $this->getLocalInvocation();

				break;
			case 'js':
				$ret = $this->getJsInvocation();

				break;
			case 'iframe':
				// Handle the IE bug: http://stackoverflow.com/questions/296674/a-resonable-request-iframes-in-ie-that-indicate-loading-progress
				if ($this->fixIEBug) {
					$script = '<script type="text/javascript">' .chr(10);
					$script .= '/*<![CDATA[*/' .chr(10);
					$script .= file_get_contents(t3lib_extMgm::extPath($this->extKey) . 'pi1/fixIEbug.js') .chr(10);
					$script .= '/*]]>*/' .chr(10);
					$script .= '</script>' .chr(10);
					
					$GLOBALS['TSFE']->additionalHeaderData[] = $script;
				}
				$ret = $this->getIframeInvocation();

				break;
		}
		// Restore local config
		$this->conf = $this->temp_conf;
		// If your openx mysql user and password are the ones used in the Typo3 Configuration file,
		// you must start the typo3 connexion again.. Just uncomment the following line
		$GLOBALS['TYPO3_DB']->connectDB();
		return $ret;

	}

	/**
	 * Returns the url of the delivery script request
	 *
	 * @param string 	$file	Filename
	 */
	function getDeliveryUrl($file,$ssl=false) {
		$ssl ? $ret = $this->OpenxServerUrls['DeliveryEngineSSL']."/".$this->OpenxDeliveryFiles[$file] : $ret = $this->OpenxServerUrls['DeliveryEngine']."/".$this->OpenxDeliveryFiles[$file];
		return $ret;
	}

	/**
	 * Get a banner from a local Openx server
	 *
	 * @return 	string
	 */
	function getLocalInvocation() {

		if (!defined('MAX_PATH')) define('MAX_PATH', t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT').$this->OpenxRootFolder);

		if (include_once(MAX_PATH . $this->OpenxFolders['Delivery'] . '/' . $this->OpenxDeliveryFiles['LocalInvocation'])) {

			if (!isset($GLOBALS['Openx_Context'])) $GLOBALS['Openx_Context'] = array();

			$openX_Infos = view_local('', $this->OpenxVars['zone'], $this->OpenxVars['campaign'], $this->OpenxVars['banner'], $this->OpenxVars['target'], $this->OpenxVars['source'], $this->OpenxVars['withtext'], $GLOBALS['Openx_Context'], $this->OpenxVars['charset']);

			if (isset($this->OpenxVars['block']) && $this->OpenxVars['block'] == '1') {
				$GLOBALS['Openx_Context'][] = array('!=' => 'bannerid:'.$openX_Infos['bannerid']);
			}

			if (isset($this->OpenxVars['blockCampaign']) && $this->OpenxVars['blockCampaign'] == '1') {
				$GLOBALS['Openx_Context'][] = array('!=' => 'campaignid:'.$openX_Infos['campaignid']);
			}
		}
		return $openX_Infos['html'];
	}

	/**
	 * Get a banner through Javascript
	 *
	 */
	function getJsInvocation () {

		$this->prepareCommonInvocationData();

		if (isset($this->OpenxVars['withtext']) && $this->OpenxVars['withtext'] != '0') {
			$this->OpenxParams['withtext'] = "withtext=1";
		}
		if (isset($this->OpenxVars['block']) && $this->OpenxVars['block'] == '1') {
			$this->OpenxParams['block'] = "block=1";
		}
		if (isset($this->OpenxVars['blockCampaign']) && $this->OpenxVars['blockCampaign'] == '1') {
			$this->OpenxParams['blockCampaign'] = "blockcampaign=1";
		}
		if (!empty($this->OpenxVars['campaign'])) {
			$this->OpenxParams['campaignid'] = "campaignid=".$this->OpenxVars['campaign'];
		}
		// The cachebuster for JS tags is auto-generated
		unset($this->OpenxParams['cb']);

		$ret = "<script type='text/javascript'><!--//<![CDATA[\n";
		$ret .= "   var m3_u = (location.protocol=='https:'?'".$this->getDeliveryUrl('AdJs',true)."':'".$this->getDeliveryUrl('AdJs')."');\n";
		$ret .= "   var m3_r = Math.floor(Math.random()*99999999999);\n";
		$ret .= "   if (!document.MAX_used) document.MAX_used = ',';\n";
		// Removed the non-XHTML compliant "language='JavaScript'
		$ret .= "   document.write (\"<scr\"+\"ipt type='text/javascript' src='\"+m3_u);\n";
		if (count($this->OpenxParams) > 0) {
			$ret .= "   document.write (\"?".implode ("&amp;", $this->OpenxParams)."\");\n";
		}
		$ret .= "   document.write ('&amp;cb=' + m3_r);\n";

		// Don't pass in exclude unless necessary
		$ret .= "   if (document.MAX_used != ',') document.write (\"&amp;exclude=\" + document.MAX_used);\n";

		if (empty($this->OpenxVars['charset'])) {
			$ret .= "   document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));\n";
		} else {
			$ret .= "   document.write ('&amp;charset=" . $this->OpenxVars['charset'] . "');\n";
		}
		$ret .= "   document.write (\"&amp;loc=\" + escape(window.location));\n";
		$ret .= "   if (document.referrer) document.write (\"&amp;referer=\" + escape(document.referrer));\n";
		$ret .= "   if (document.context) document.write (\"&context=\" + escape(document.context));\n";

		// Pass in if the FlashObject - Inline code has already been passed in
		$ret .= "   if (document.mmm_fo) document.write (\"&amp;mmm_fo=1\");\n";
		$ret .= "   document.write (\"'><\\/scr\"+\"ipt>\");\n";
		$ret .= "//]]>--></script>";

		$ret .= "<noscript>{$this->OpenxOpenxBackupImage}</noscript>\n";

		return $ret;
	}

	/**
	 * Get a banner using iframes
	 *
	 */
	function getIframeInvocation() {

		$this->prepareCommonInvocationData();

		if (isset($this->OpenxVars['refresh']) && $this->OpenxVars['refresh'] != '') {
			$this->OpenxParams['refresh'] = "refresh=".$this->OpenxVars['refresh'];
		}
		if (isset($this->OpenxVars['resize']) && $this->OpenxVars['resize'] == '1') {
			$this->OpenxParams['resize'] = "resize=1";
		}


		$urlSource = $this->getDeliveryUrl('AdFrame');
		
		if (sizeof($this->OpenxParams) > 0) {
			$urlSource .= "?".implode ("&amp;", $this->OpenxParams);
		}
		
		if ($this->fixIEBug) {
			$ret = '<script type="text/javascript">openxURL["' . $this->uniqueid . '"] = "' . $urlSource . '"</script>';
			$ret .= '<iframe id="' . $this->uniqueid . '" name="' . $this->uniqueid . '" src=""';
		}
		else {	
			$ret = '<iframe id="' . $this->uniqueid . '" name="' . $this->uniqueid . '" src="' . $urlSource . '"';
		}
		$ret .= ' framespacing="0" frameborder="no" scrolling="no"';
	
		if (isset($this->OpenxVars['framewidth']) && $this->OpenxVars['framewidth'] != '' && $this->OpenxVars['framewidth'] != '-1') {
			$ret .= ' width="' . $this->OpenxVars['framewidth'] . '"';
		}
		if (isset($this->OpenxVars['frameheight']) && $this->OpenxVars['frameheight'] != '' && $this->OpenxVars['frameheight'] != '-1') {
			$ret .= ' height="' . $this->OpenxVars['frameheight'] . '"';
		}
		if (isset($this->OpenxVars['transparent']) && $this->OpenxVars['transparent'] == '1') {
			$ret .= ' allowtransparency="true"';
		}

		$ret .= ">";


		if (isset($this->OpenxVars['refresh']) && $this->OpenxVars['refresh'] != '') {
			unset ($this->OpenxParams['refresh']);
		}
		if (isset($this->OpenxVars['resize']) && $this->OpenxVars['resize'] == '1') {
			unset ($this->OpenxParams['resize']);
		}

		$ret .= $this->OpenxOpenxBackupImage;

		$ret .= "</iframe>\n";

		if (isset($this->OpenxVars['target']) && $this->OpenxVars['target'] != '') {
			$this->OpenxParams['target'] = "target=".urlencode($this->OpenxVars['target']);
		}
		return $ret;
	}

	/**
     * Prepare data before generating the invocation code
     *
     */
	function prepareCommonInvocationData()
	{

		$this->OpenxMacros = array(
		'cachebuster' => rand(0,time())
		);

		$this->OpenxParams = array();

		if (!isset($this->OpenxVars['withtext'])) {
			$this->OpenxVars['withtext'] = 0;
		}

		// Set parameters
		if (isset($this->OpenxVars['clientid']) && strlen($this->OpenxVars['clientid']) && $this->OpenxVars['clientid'] != '0') {
			$this->OpenxParams['clientid'] = "clientid=".$this->OpenxVars['clientid'];
		}
		if (isset($this->OpenxVars['zone']) && $this->OpenxVars['zone'] != '') {
			$this->OpenxParams['zoneid'] = "zoneid=".urlencode($this->OpenxVars['zone']);
		}
		if (isset($this->OpenxVars['campaign']) && strlen($this->OpenxVars['campaign']) && $this->OpenxVars['campaign'] != '0') {
			$this->OpenxParams['campaignid'] = "campaignid=".$this->OpenxVars['campaign'];
		}
		if (isset($this->OpenxVars['banner']) && $this->OpenxVars['banner'] != '') {
			$this->OpenxParams['bannerid'] = "bannerid=".urlencode($this->OpenxVars['banner']);
		}
		if (isset($this->OpenxVars['what']) && $this->OpenxVars['what'] != '') {
			$this->OpenxParams['what'] = "what=".str_replace (",+", ",_", $this->OpenxVars['what']);
		}
		if (isset($this->OpenxVars['source']) && $this->OpenxVars['source'] != '') {
			$this->OpenxParams['source'] = "source=".urlencode($this->OpenxVars['source']);
		}
		if (isset($this->OpenxVars['target']) && $this->OpenxVars['target'] != '') {
			$this->OpenxParams['target'] = "target=".urlencode($this->OpenxVars['target']);
		}
		if (isset($this->OpenxVars['charset']) && $this->OpenxVars['charset'] != '') {
			$this->OpenxParams['charset'] = "charset=".urlencode($this->OpenxVars['charset']);
		}
		if (!empty($this->OpenxVars['cb'])) {
			$this->OpenxParams['cb'] = "cb=" . $this->OpenxMacros['cachebuster'];
		}

		// Set $this->OpenxBackupImage to the HTML for the backup image (same as used by adview)
		$hrefParams = array();
		$imgParams = $this->OpenxParams;
		$this->uniqueid = 'a'.substr(md5(uniqid('', 1)), 0, 7);

		if ((isset($this->OpenxVars['banner'])) && ($this->OpenxVars['banner'] != '')) {
			$hrefParams[] = "bannerid=".$this->OpenxVars['banner'];
			$hrefParams[] = "zoneid=".$this->OpenxVars['zone'];
		} else {
			$hrefParams[] = "n=".$this->uniqueid ;
			$imgParams[] = "n=".$this->uniqueid ;
		}
		if (!empty($this->OpenxVars['cb']) || !isset($this->OpenxVars['cb'])) {
			$hrefParams[] = "cb=" . $this->OpenxMacros['cachebuster'];
		}
		// Make sure that ct0= is the last element in the array
		unset($imgParams['ct0']);

		$backup = '<a href="' . $this->getDeliveryUrl('AdClick') . '?' . implode("&amp;", $hrefParams) . '"';

		if (isset($this->OpenxVars['target']) && $this->OpenxVars['target'] != '') {
			$backup .= ' target="' . $this->OpenxVars['target'] . '"';
		}
		else {
			$backup .= ' target="_blank"';
		}

		$backup .= '><img src="' . $this->getDeliveryUrl('AdView');
		// Remove any paramaters that should not be passed into the IMG call
		unset($imgParams['target']);
		if (sizeof($imgParams) > 0) $backup .= "?".implode ("&amp;", $imgParams);
		$backup .= '" border="0" alt="" /></a>';

		$this->OpenxBackupImage = $backup;
	}


	/**
	 * This method performs various initialisations
	 *
	 * @param	array		$conf: plugin configuration, as received by the main() method
	 * @return	void
	 */
	function init($conf) {
		$this->conf = $conf; // Base configuration is equal the the plugin's TS setup

		// Load the flexform and loop on all its values to override TS setup values
		// Some properties use a different test (more strict than not empty) and yet some others no test at all

		$this->pi_initPIflexForm();
		if (is_array($this->cObj->data['pi_flexform']['data'])) {
			foreach ($this->cObj->data['pi_flexform']['data'] as $sheet => $langData) {
				foreach ($langData as $lang => $fields) {
					foreach ($fields as $field => $value) {
						$value = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $field, $sheet);
						$this->conf[$field] = $value;
					}
				}
			}
		}
		
		// Tells if we the browser is IE and if some fixing must applied
		$informations = t3lib_div::clientInfo();
		if ($this->conf['fixIEBug'] && $informations['BROWSER'] == 'msie') {
			$this->fixIEBug = 1;
		}
		
// Handle local TypoScript override
		if (!empty($this->conf['flexformTS'])) {
			$typoscript = t3lib_TSparser::checkIncludeLines($this->conf['flexformTS']); // Check for file inclusion
			$parseObj = t3lib_div::makeInstance('t3lib_TSparser'); // Instantiate a TS parser
			$parseObj->parse($typoscript); // Parse the local TypoScript
			if (isset($parseObj->setup['zone.'])) $parseObj->setup['zone'] = $this->cObj->stdWrap($parseObj->setup['zone'],$parseObj->setup['zone.']);
			$this->conf = t3lib_div::array_merge_recursive_overrule($this->conf, $parseObj->setup); // Merge with local configuration
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/openx/pi1/class.tx_openx_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/openx/pi1/class.tx_openx_pi1.php']);
}

?>