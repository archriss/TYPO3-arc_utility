<?php

namespace Archriss\ArcUtility\Scheduler;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Christophe Monard <cmonard@archriss.com>
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
 * Plugin 'FileCleanerFieldProvider' for the 'arc_utility' extension.
 *
 * @author	Christophe Monard <cmonard@archriss.com>
 * @package	TYPO3
 * @subpackage	arc_utility
 */
class FileCleanerFieldProvider implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {

    protected $LLL = 'LLL:EXT:arc_utility/Resources/Private/Language/locallang_filecleaner.xlf';
    protected $fieldArray = array(
        'dirs' => array(
            'type' => 'area',
            'default' => '',
            'cols' => 45,
            'rows' => 8,
            'hint' => TRUE,
        ),
        'days' => array(
            'type' => 'text',
            'default' => '',
            'size' => 10,
            'hint' => FALSE,
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
                case 'checkbox':
                    $checked = '';
                    $fieldDefault = $field['default'];
                    // Initialize extra field value
                    if (empty($taskInfo[$fieldName])) {
                        if ($schedulerModule->CMD == 'edit' && $task->$fieldName) {
                            $taskInfo[$fieldName] = $task->$fieldName;
                        } else {
                            $taskInfo[$fieldName] = $fieldDefault;
                        }
                    }
                    if ($taskInfo[$fieldName] == 1) {
                        $checked = 'checked="cheched" ';
                    }
                    // Write the code for the field
                    $fieldCode = '<input type="checkbox" name="tx_scheduler[' . $fieldName . ']" id="' . $fieldID . '" value="1" ' . $checked . '/>';
                    break;
                case 'select':
                    $items = '';
                    foreach ($field['items'] as $item) {
                        if ($schedulerModule->CMD == 'edit' && $task->$fieldName && $task->$fieldName == $item) {
                            $selected = ' selected';
                        } else {
                            $selected = '';
                        }
                        $items .= '<option value="' . $item . '"' . $selected . '>' . $GLOBALS['LANG']->sL($LLL . '.' . $item) . '</option>';
                    }
                    $class = '';
                    if (isset($field['class'])) {
                        $class = ' class="' . $field['class'] . '"';
                    }
                    $fieldCode = '<select id="' . $fieldID . '" name="tx_scheduler[' . $fieldName . ']"' . $class . '>' . $items . '</select>';
                    break;
                case 'area':
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
                if ($field['hint']) {
                    $additionalFields[$fieldName]['cshKey'] = '_MOD_system_txschedulerM1';
                    $additionalFields[$fieldName]['cshLabel'] = $fieldID;
                }
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
        $submittedData['dirs'] = implode(chr(10), \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(chr(10), $submittedData['dirs'], TRUE));
        $submittedData['days'] = \TYPO3\CMS\Core\Utility\MathUtility::forceIntegerInRange($submittedData['days'], 0);
        return TRUE;
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
