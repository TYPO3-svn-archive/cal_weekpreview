<?php

########################################################################
# Extension Manager/Repository config file for ext: "cal_weekpreview"
#
# Auto generated 17-06-2009 08:51
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'cal base view for the next weeks (based on month view)',
	'description' => 'Provides a customized view (based on the month view) to give a comprehensive preview over the next week\'s events, starting in the current week.',
	'category' => 'plugin',
	'author' => 'Thomas Kowtsch',
	'author_email' => 'typo3@thomas-kowtsch.de',
	'shy' => '',
	'dependencies' => 'cal',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'cal' => '1.2.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:8:{s:9:"ChangeLog";s:4:"22a1";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"13f2";s:19:"doc/wizard_form.dat";s:4:"56a9";s:20:"doc/wizard_form.html";s:4:"0a35";s:42:"static/week_preview_template/constants.txt";s:4:"2696";s:38:"static/week_preview_template/setup.txt";s:4:"85d2";}',
);

?>