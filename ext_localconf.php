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
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Archriss\ArcUtility\Scheduler\MailTester'] = array(
    'extension' => $_EXTKEY,
    'title' => 'Archriss - Utility Mail Tester',
    'description' => 'This task test mail sending to specified address.',
    'additionalFields' => 'Archriss\ArcUtility\Scheduler\MailTesterFieldProvider',
);