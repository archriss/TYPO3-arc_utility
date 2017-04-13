<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Scheduler
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Archriss\ArcUtility\Scheduler\DateTimeChecker'] = array(
    'extension' => $_EXTKEY,
    'title' => 'Archriss - Utility Datetime Checker',
    'description' => 'This task test timestamp generation.',
);
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Archriss\ArcUtility\Scheduler\ContextChecker'] = array(
    'extension' => $_EXTKEY,
    'title' => 'Archriss - Utility TYPO3 Context Checker',
    'description' => 'This task test context used for scheduler.',
);
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Archriss\ArcUtility\Scheduler\MailTester'] = array(
    'extension' => $_EXTKEY,
    'title' => 'Archriss - Utility Mail Tester',
    'description' => 'This task test mail sending to specified address.',
    'additionalFields' => 'Archriss\ArcUtility\Scheduler\MailTesterFieldProvider',
);
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Archriss\ArcUtility\Scheduler\FileCleaner'] = array(
    'extension' => $_EXTKEY,
    'title' => 'Archriss - File Cleaner',
    'description' => 'This task clean files from some directory after some days.',
    'additionalFields' => 'Archriss\ArcUtility\Scheduler\FileCleanerFieldProvider',
);
