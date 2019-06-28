<?php

namespace Archriss\ArcUtility\Userfuncs;

/* * ************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Christophe Monard <cmonard@archriss.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

class TcaTitle {

    public function categoryTitle(&$parameters, $parentObject) {
        // get all caregories
        $categories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_category', 'deleted = 0 AND pid = ' . $parameters['row']['pid'], '', '', '', 'uid');
        $row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('uid, title, parent', 'sys_category', 'deleted = 0 AND uid = ' . $parameters['row']['uid']);
        if ($row['parent'] > 0) {
            $newTitle = '';
            $parent = $row['parent'];
            while ($categories[$parent]['parent'] && $categories[$parent]['parent'] > 0) {
                $parent = $categories[$parent]['parent'];
                $newTitle.= '- ';
            }
            $newTitle.= '- ' . $row['title'];
        } else {
            $newTitle = $row['title'];
        }
        $parameters['title'] = $newTitle;
    }

}
