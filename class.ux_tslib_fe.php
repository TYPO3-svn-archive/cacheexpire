<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2008 Martin Holtz (typo3@martinholtz.de)
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

// require_once (PATH_t3lib.'class.t3lib_div.php');
/** 
 * Plugin 'Cache Expire'
 *
 * @author	Martin Holtz <typo3@martinholtz.de>
 */
class ux_tslib_fe extends tslib_fe {

	/*
	function __construct() {
		echo "__construct()";
	}
	
	function ux_tslib_fe() {
		$this->__construct();
	}
	*/

	/**
	 * Sets cache content; Inserts the content string into the cache_pages table.
	 * It is a copy of the original Function (Revision: #4014, from 24.08.2008)
	 *
	 * @param	string		The content to store in the HTML field of the cache table
	 * @param	mixed		The additional cache_data array, fx. $this->config
	 * @param	integer		Timestamp
	 * @return	void
	 * @see realPageCacheContent(), tempPageCacheContent()
	 */
	function setPageCacheContent($content,$data,$tstamp)	{
					
		$this->clearPageCacheContent();
		
		/* Start new CODE */
		
		// Config
		// TODO: use it
		$config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['cacheexpire']);
	
		// TODO: write an hook, so somebody else can add additional stuff?
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_content', 'tt_content.pid='.intval($this->id).' AND tt_content.hidden=0 AND tt_content.deleted=0 AND (starttime > 0 OR endtime > 0) ');
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			// $tstamp is the orignial expire-date of that page
			// usually it is calculated by cache-expiredate and 
			// $GLOBALS['EXEC_TIME']
			// the page starttime/endtime is checked before
			// it is requested from cache. So we do not have to care
			// of starttime/endtime of the page itself 
			
			// we want to respect the starttime / endtime of the
			// content elements 
			// 
			// we have to check for each content element only, if it has a starttime
			// or an endtime which takes effect betwwen $GLOBALS['EXEC_TIME']
			// and the default-expire date. 
			if ($row['starttime'] > $GLOBALS['EXEC_TIME'] 
				&& $row['starttime'] < $tstamp) {
				if (TYPO3_DLOG)	{ t3lib_div::devLog('Expires was: '.$tstamp.' new Timestamp via starttime is: '.$row['starttime'].' (ID='.$row['id'].')','cacheexpire',0,$row); } 
				$tstamp = $row['starttime'];
			}
			if ($row['endtime'] > $GLOBALS['EXEC_TIME']
				&& $row['endtime'] < $tstamp) {
					if (TYPO3_DLOG)	{ t3lib_div::devLog('Expires was: '.$tstamp.' new Timestamp via endtime is: '.$row['endtime'].' (ID='.$row['id'].')','cacheexpire',0,$row); }
					$tstamp = $row['endtime'];
				}
		}
		/* END new Code */
		
		$insertFields = array(
			'hash' => $this->newHash,
			'page_id' => $this->id,
			'HTML' => $content,
			'temp_content' => $this->tempContent,
			'cache_data' => serialize($data),
			'expires' => $tstamp,
			'tstamp' => $GLOBALS['EXEC_TIME']
		);

		$this->cacheExpires = $tstamp;

		if ($this->page_cache_reg1)	{
			$insertFields['reg1'] = intval($this->page_cache_reg1);
		}
		$this->pageCachePostProcess($insertFields,'set');

		$GLOBALS['TYPO3_DB']->exec_INSERTquery('cache_pages', $insertFields);
	}
	

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cacheexpire/class.ux_tslib_fe.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cacheexpire/class.ux_tslib_fe.php']);
}

?>