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
 * Plugin 'SysCategoryOrganizer' for the 'arc_utility' extension.
 *
 * @author	Christophe Monard <cmonard@archriss.com>
 * @package	TYPO3
 * @subpackage	arc_utility
 */
class SysCategoryOrganizer extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

    // Field from task settings
    public $pids = '';
    public $step = 1;

    public function execute() {
        if ($this->pids == '') {
            return FALSE;
        } else {
            foreach (\TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $this->pids) as $storage) {
                $sortedCategories = array();
                // main categories first please :)
                $mainCategories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_category', 'deleted = 0 AND parent = 0 AND pid = ' . $storage, '', 'sorting ASC');
                if ($mainCategories) {
                    $sorting = $this->step;
                    foreach ($mainCategories as $category) {
                        $sortedCategories[$category['uid']] = $sorting;
                        $sorting+= $this->step;
                        $this->getSubCategories($category['uid'], $storage, $sorting, $sortedCategories); // recursive call
                    }
                }
                // Sort db
                if (count($sortedCategories) > 0) {
                    foreach ($sortedCategories as $sortedCategoryUid => $sortedCategorySorting) {
                        $GLOBALS['TYPO3_DB']->exec_UPDATEquery('sys_category', 'uid = ' . $sortedCategoryUid, array('sorting' => $sortedCategorySorting));
                    }
                }
            }
        }
        return TRUE;
    }

    protected function getSubCategories($parent, $storage, &$sorting, &$sortedCategories) {
        $subCategories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_category', 'deleted = 0 AND parent = ' . $parent . ' AND pid = ' . $storage, '', 'sorting ASC');
        if ($subCategories) {
            foreach ($subCategories as $subCategory) {
                $sortedCategories[$subCategory['uid']] = $sorting;
                $sorting+= $this->step;
                $this->getSubCategories($subCategory['uid'], $storage, $sorting, $sortedCategories); // recursive call
            }
        }
    }

    /**
     * This method is designed to return some additional information about the task,
     * that may help to set it apart from other tasks from the same class
     * This additional information is used - for example - in the Scheduler's BE module
     * This method should be implemented in most task classes
     *
     * @return string Information to display
     */
    public function getAdditionalInformation() {
        return $this->pids != '' ? 'Reorganising sys_category from pids : ' . $this->pids : 'No pids selected, nothing will happend';
    }

}
