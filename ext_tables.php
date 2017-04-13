<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add context sensitive help (csh) to the backend module
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    '_MOD_system_txschedulerM1',
    'EXT:arc_utility/Resources/Private/Language/locallang_csh_scheduler.xlf'
);
