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
 * Plugin 'DateTimeChecker' for the 'arc_utility' extension.
 *
 * @author	Christophe Monard <cmonard@archriss.com>
 * @package	TYPO3
 * @subpackage	arc_utility
 */
class DateTimeChecker extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

    /**
     * Main function of the scheduler, connect, collect and parse data from Tourinsoft
     *
     * @return boolean
     */
    public function execute() {
    	// Définition de la timezone (non récupéré de TYPO3_CONF_VAR)
        $datetimeZone = new \DateTimeZone('Europe/Paris');

        $rtimeObj = new \DateTime;

        $btimeObj = \DateTime::createFromFormat('d/m/Y H:i:s', '19/04/2016 00:00:00');
        $btimeObj->setTimezone($datetimeZone);

        $manual = new \DateTime;
        $manual->setTimezone($datetimeZone);
        $manual->setDate(2016, 4, 19);
        $manual->setTime(0, 0, 0);

        \TYPO3\CMS\Core\Utility\GeneralUtility::devLog(__CLASS__, 'ArcUtility', 0, array(
            'timezone' => $datetimeZone,
            'current time' => $rtimeObj->getTimestamp(),
            'generated midnight' => $btimeObj->getTimestamp(),
            'manual' => $manual->getTimestamp(),
        ));
        return TRUE;
    }

}
