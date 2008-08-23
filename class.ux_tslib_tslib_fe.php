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

require_once (PATH_t3lib.'class.t3lib_div.php');
/** 
 * Plugin 'Cache Expire'
 *
 * @author	Martin Holtz <typo3@martinholtz.de>
 */
class ux_tslib_tslib_fe extends tslib_fe {



	/**
	 * Sets cache content; Inserts the content string into the cache_pages table.
	 *
	 * @param	string		The content to store in the HTML field of the cache table
	 * @param	mixed		The additional cache_data array, fx. $this->config
	 * @param	integer		Timestamp
	 * @return	void
	 * @see realPageCacheContent(), tempPageCacheContent()
	 */
	function setPageCacheContent($content,$data,$tstamp)	{
		// Config
		// TODO: use it
		$config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['cacheexpire']);
	
	
		// TODO: write an hook, so somebody else can add additional stuff
	
		$this->clearPageCacheContent();
// MH : XCLASS tslib_fe::setPageCacheContent...?	
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tt_content', 'tt_content.pid='.intval($this->id).' AND tt_content.hidden=0 AND tt_content.deleted=0 AND (starttime > 0 OR endtime > 0) ');
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			// starttime 	endtime
			// not shown yet, but starttime is before expire-date
			// starttime must be in future to have an influence
			if ($row['starttime'] > $GLOBALS['EXEC_TIME'] 
				&& $row['starttime'] < $tstamp) {
				$tstamp = $row['starttime'];
			}
			if ($row['endtime'] > $GLOBALS['EXEC_TIME']
				&& $row['endtime'] < $tstamp) {
				$tstamp = $row['endtime'];
			}
		}
		
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cacheexpire/class.ux_tslib_tslib_fe.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cacheexpire/class.ux_tslib_tslib_fe.php']);
}

?>