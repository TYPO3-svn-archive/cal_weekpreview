<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

/**
 *  Both views and model are provided using TYPO3 services.  Models should be
 *  of the type 'cal_model' with a an extension key specific to that model.
 *  Views can be of two types.  The 'cal_view' type is used for views that 
 *  display multiple days.  Within this type, subtypes for 'single', 'day', 
 *  'week', 'month', 'year', and 'custom' are available.  The default views 
 *  each have the key 'default'.  Custom views tied to a specific model should 
 *  have service keys identical to the key of that model.
 */

t3lib_extMgm::addService($_EXTKEY,  'cal_view' /* sv type */,  'tx_default_month' /* sv key */,
        array(
                'title' => 'Default Month View', 'description' => '', 'subtype' => 'month',
                'available' => TRUE, 'priority' => 55, 'quality' => 50,
                'os' => '', 'exec' => '',
                'classFile' => t3lib_extMgm::extPath($_EXTKEY).'view/class.tx_cal_weekpreview.php',
                'className' => 'tx_cal_weekpreview',
        )
);


?>