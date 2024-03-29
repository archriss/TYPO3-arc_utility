<?php

namespace Archriss\ArcUtility\Scheduler;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Christophe Monard <cmonard@archriss.com>
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
 * ************************************************************* */

/**
 * Plugin 'MailTesterFieldProvider' for the 'arc_utility' extension.
 *
 * @author	Christophe Monard <cmonard@archriss.com>
 * @package	TYPO3
 * @subpackage	arc_utility
 */
class MailTesterFieldProvider implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {

    protected $LLL = 'LLL:EXT:arc_utility/Resources/Private/Language/locallang_mailtester.xlf';
    protected $fieldArray = array(
        'receiverName' => array(
            'type' => 'field',
            'default' => '',
            'size' => 30,
        ),
        'receiverMail' => array(
            'type' => 'mail',
            'default' => '',
            'size' => 30,
        ),
        'senderName' => array(
            'type' => 'field',
            'default' => '',
            'size' => 30,
        ),
        'senderMail' => array(
            'type' => 'mail',
            'default' => '',
            'size' => 30,
        ),
        'replytoMail' => array(
            'type' => 'mail',
            'default' => '',
            'size' => 30,
        ),
        'returnPath' => array(
            'type' => 'mail',
            'default' => '',
            'size' => 30,
        ),
        'subject' => array(
            'type' => 'field',
            'default' => 'Archriss Test Email',
            'size' => 30,
        ),
        'body' => array(
            'type' => 'text',
            'default' => '',
            'cols' => 45,
            'rows' => 8,
        ),
    );

    /**
     * Gets additional fields to render in the form to add/edit a task
     *
     * @param array $taskInfo Values of the fields from the add/edit task form
     * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task The task object being edited. Null when adding a task!
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the scheduler backend module
     * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
     */
    public function getAdditionalFields(array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule) {
        $additionalFields = array();
        foreach ($this->fieldArray as $fieldName => $field) {
            $fieldCode = '';
            $fieldSize = $field['size'];
            $fieldID = 'task_' . $fieldName;
            $LLL = $this->LLL . ':field.' . $fieldName;
            switch ($field['type']) {
                case 'text':
                    $fieldDefault = $field['default'];
                    // Initialize extra field value
                    if (empty($taskInfo[$fieldName])) {
                        if ($schedulerModule->CMD == 'edit' && $task->$fieldName) {
                            $taskInfo[$fieldName] = $task->$fieldName;
                        } else {
                            $taskInfo[$fieldName] = $fieldDefault;
                        }
                    }
                    // Write the code for the field
                    $fieldCode = '<textarea name="tx_scheduler[' . $fieldName . ']" id="' . $fieldID . '" cols="' . $field['cols'] . '" rows="' . $field['rows'] . '">' . $taskInfo[$fieldName] . '</textarea>';
                    break;
                default:
                    $type = $field['type'] == 'password' ? 'password' : 'text';
                    $fieldDefault = $field['default'];
                    // Initialize extra field value
                    if (empty($taskInfo[$fieldName])) {
                        if ($schedulerModule->CMD == 'edit' && $task->$fieldName) {
                            $taskInfo[$fieldName] = $task->$fieldName;
                        } else {
                            $taskInfo[$fieldName] = $fieldDefault;
                        }
                    }
                    // Write the code for the field
                    $fieldCode = '<input type="' . $type . '" name="tx_scheduler[' . $fieldName . ']" id="' . $fieldID . '" value="' . $taskInfo[$fieldName] . '" size="' . $fieldSize . '" />';
                    break;
            }
            if ($fieldCode != '') {
                $additionalFields[$fieldName] = array(
                    'code' => $fieldCode,
                    'label' => $LLL,
                );
            }
        }
        return $additionalFields;
    }

    /**
     * Validates the additional fields' values
     *
     * @param array $submittedData An array containing the data submitted by the add/edit task form
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the scheduler backend module
     * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
     */
    public function validateAdditionalFields(array &$submittedData, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule) {
        // checks
        $isOK = TRUE;
        foreach ($this->fieldArray as $field => $config) {
            if ($config['type'] == 'mail' && $submittedData[$field] != '' && !\TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($submittedData[$field])) {
                $submittedData[$field] = '';
                $isOK = FALSE;
            }
        }
        return $isOK;
    }

    /**
     * Takes care of saving the additional fields' values in the task's object
     *
     * @param array $submittedData An array containing the data submitted by the add/edit task form
     * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task Reference to the scheduler backend module
     * @return void
     */
    public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task) {
        foreach (array_keys($this->fieldArray) as $fieldName) {
            $task->$fieldName = $submittedData[$fieldName];
        }
    }

}
