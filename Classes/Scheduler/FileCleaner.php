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
 * Plugin 'FileCleaner' for the 'arc_utility' extension.
 *
 * @author	Christophe Monard <cmonard@archriss.com>
 * @package	TYPO3
 * @subpackage	arc_utility
 */
class FileCleaner extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

    // Field from task settings
    public $dirs = '';
    public $days = 0;

    public function execute() {
        // Proceed only if we have some dirs and days are positive
        if ($this->dirs != '' && $this->days > 0) {
            $maxtime = $GLOBALS['EXEC_TIME'] - ($this->days * 86400);
            $dirs = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(chr(10), $this->dirs);
            foreach ($dirs as $dir) {
                // Get all files in dir sorted by mtime
                $files = \TYPO3\CMS\Core\Utility\GeneralUtility::getFilesInDir(PATH_site . $dir, '', TRUE, 'mtime');
                if (is_array($files) && count($files)) {
                    foreach ($files as $file) {
                        // test each file, break on first with good time (all following will be keeped)
                        if (filemtime($file) < $maxtime) {
                            @unlink($file);
                        } else {
                            break;
                        }
                    }
                }
            }
        }
        return TRUE;
    }

    public function getAdditionalInformation() {
        return 'Cleaning file with mtime over ' . $this->days . ' days from : ' . chr(10) . $this->dirs;
    }

}
