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
/** 
 * Plugin 'Cache Expire'
 *
 * @author	Martin Holtz <typo3@martinholtz.de>
 */


if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['tslib/class.tslib_fe.php']=t3lib_extMgm::extPath('cacheexpire').'class.ux_tslib_fe.php';

// $TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_cacheexpire_timestamp'][] = t3lib_extMgm::extPath('cacheexpire').'class.user_tt_news_cacheexpire.php:&user_tt_news_cacheexpire'; 


?>