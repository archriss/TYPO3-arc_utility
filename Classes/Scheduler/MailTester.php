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
 * Plugin 'MailTester' for the 'arc_utility' extension.
 *
 * @author	Christophe Monard <cmonard@archriss.com>
 * @package	TYPO3
 * @subpackage	arc_utility
 */
class MailTester extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

    // Field from task settings
    public $senderName = '';
    public $senderMail = '';
    public $receiverName = '';
    public $receiverMail = '';
    public $replytoMail = '';
    public $returnPath = '';
    public $subject = '';
    public $body = '';

    // errorLog
    protected $errorLog = '';

    public function execute() {
        $params = array(
            'to' => array(($this->receiverName != '' ? $this->receiverName : 0) => $this->receiverMail),
            'sender' => array(($this->senderName != '' ? $this->senderName : 0) => $this->senderMail),
            'from' => array(($this->senderName != '' ? $this->senderName : 0) => $this->senderMail),
            'subject' => $this->subject,
            'body' => $this->body,
        );
        if ($this->replytoMail != '') {
            $params['reply'] = $this->replytoMail;
        }
        if ($this->returnPath != '') {
            $params['return_path'] = $this->returnPath;
        }
        $result = self::sendMail($params);
        \TYPO3\CMS\Core\Utility\GeneralUtility::devLog(__CLASS__, 'ArcUtility', 0, array('mailParams' => $params, 'mailResults' => $result));
        $this->errorLog = 'Success: ' . $result['sent'] . '; Failed: ' . $result['fail'];
        $this->save();
        if ($result['fail']) {
            return FALSE;
        }
        return TRUE;
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
        return $this->errorLog;
    }

    /**
     * Mailing function
     *
     * @param	array		$param Array of configuration of the mail
     * 					// sender format : array('email' => validEmail[, 'name' => name])
     * 					// to, cc, bcc, from, reply format : array(name => validEmail[, validEmail[, ...]])
     *      				// return_path, read_receipt : string validEmail
     * 					// priority : integer 1 => 'Highest', 2 => 'High', 3 => 'Normal', 4 => 'Low', 5 => 'Lowest'
     * 					// subject : string
     * 					// body : array('text' => string[, 'format' => 'text/html'])
     * 					// embed, files : array(newFileName => filePathAndName[, filePathAndName[, ...]]) - for embed newFileName will not be used
     * @return	FALSE when missing parameters, otherwise array of result (sent, fail)
     */
    protected static function sendMail($param = array()) {
        if (count($param > 0) && isset($param['to']) && isset($param['body'])) {
            /* @var $mail \TYPO3\CMS\Core\Mail\MailMessage */
            $mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
            // To
            foreach ((array) $param['to'] as $to_name => $to_email) {
                $mail->addTo($to_email, (!is_numeric($to_name) ? $to_name : NULL));
            }
            // Sender
            if (isset($param['sender']) && $param['sender']['email']) {
                $mail->setSender($param['sender']['email'], (isset($param['sender']['name']) ? $param['sender']['name'] : NULL));
            }
            // From
            if (isset($param['from'])) {
                foreach ((array) $param['from'] as $from_name => $from_email) {
                    $mail->addFrom($from_email, (!is_numeric($from_name) ? $from_name : NULL));
                }
            }
            // Reply To
            if (isset($param['reply'])) {
                foreach ((array) $param['reply'] as $reply_name => $reply_email) {
                    $mail->addReplyTo($reply_email, (!is_numeric($reply_name) ? $reply_name : NULL));
                }
            }
            // Return Path
            if (isset($param['return_path'])) {
                $mail->setReturnPath($param['return_path']);
            }
            // Subject
            if (isset($param['subject'])) {
                $mail->setSubject($param['subject']);
            }
            // Bodytext
            $mail->setBody($param['body'], 'text/plain');
            // Send the mail !
            $sent = $mail->send();
            // Failled send
            $fail = $mail->getFailedRecipients();
            // Return the array of result
            return array('sent' => $sent, 'fail' => $fail);
        } else {
            return FALSE;
        }
    }

}
