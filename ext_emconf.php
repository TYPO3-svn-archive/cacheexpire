<?php

########################################################################
# Extension Manager/Repository config file for ext: "cacheexpire"
#
# Auto generated 23-08-2008 20:29
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Cache Expire',
	'description' => 'Page Cache Expires cares of tt_content starttime/endtime',
	'category' => 'be',
	'shy' => 0,
	'version' => '0.0.1',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'test',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Martin Holtz',
	'author_email' => 'typo3@martinholtz.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.2.0-0.0.0',
			'php' => '5.0.0-0.0.0',
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:5:{s:27:"class.ux_tslib_tslib_fe.php";s:4:"55b2";s:21:"ext_conf_template.txt";s:4:"5a69";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"207d";s:10:"readme.txt";s:4:"f3a5";}',
	'suggests' => array(
	),
);

?>