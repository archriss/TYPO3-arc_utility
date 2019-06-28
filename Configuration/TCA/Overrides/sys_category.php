<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['arc_utility']);
if ($conf['sys_cat_title']) {
    $GLOBALS['TCA']['sys_category']['ctrl']['label_userFunc'] = 'Archriss\\ArcUtility\\Userfuncs\\TcaTitle->categoryTitle';
}
