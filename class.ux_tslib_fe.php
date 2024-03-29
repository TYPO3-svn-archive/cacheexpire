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

			// only if content is not "message_page_is_being_generated"
			// which expires after 30 seconds
		if (!$this->tempContent) {
				// can be changed from an extension via $GLOBALS['TSFE']->cacheExpires
			if ($this->cacheExpires > 0 && $tstamp > $this->cacheExpires) { $tstamp = $this->cacheExpires; }

				// Call hook for $tstamp calulating
				// TODO: implement the interface
			if(is_array($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['setPageCacheContent'])) {
				foreach($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['setPageCacheContent'] as $classData) {
					$hookObject = &t3lib_div::getUserObj($classData);

					if(!($hookObject instanceof tslib_fe_setPageCacheContentHook)) {
							// TODO: should be an timestamp
						throw new UnexpectedValueException('$hookObject must implement interface tslib_content_tslib_fe_setPageCacheContentHook', 1);
					}
					$tstamp = $hookObject->getCacheExpiresTimestamp($tstamp, $this->id, $data);
				}
			}

			$pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');

				// Workspace does not matter, because they are on an different page
				// so whe can use $this-id to check for content elements on this page
				// Versioning does not matter, because they got pid = -1
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tt_content',
				'tt_content.pid='.intval($this->id).' AND (	(tt_content.starttime > '.$GLOBALS['EXEC_TIME'].' AND tt_content.starttime < '.$tstamp.') OR (tt_content.endtime > '.$GLOBALS['EXEC_TIME'].' AND tt_content.endtime < '.$tstamp.')) '.$pageSelect->enableFields('tt_content',0,array('starttime' => true,'endtime' => true),FALSE));

			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
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
					// and the default-expire date ($tstamp).
				if ($row['starttime'] < $tstamp && $row['starttime'] > $GLOBALS['EXEC_TIME']) {
					$tstamp = $row['starttime'];
				}
				if ($row['endtime'] < $tstamp && $row['endtime'] > $GLOBALS['EXEC_TIME']) {
					$tstamp = $row['endtime'];
				}
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cacheexpire/class.ux_tslib_fe.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cacheexpire/class.ux_tslib_fe.php']);
}

?>