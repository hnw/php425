<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of the PEAR Console_CommandLine package.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Console 
 * @package   Console_CommandLine
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   CVS: $Id: Password.php,v 1.4 2008/05/22 08:49:22 izi Exp $
 * @link      http://pear.php.net/package/Console_CommandLine
 * @since     File available since release 0.1.0
 */

/**
 * Required by this class.
 */
require_once 'Console/CommandLine/Action.php';

/**
 * Class that represent the Password action, a special action that allow the 
 * user to specify the password on the commandline or to be prompted for 
 * entering it.
 *
 * @category  Console
 * @package   Console_CommandLine
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @copyright 2007 David JEAN LOUIS
 * @license   http://opensource.org/licenses/mit-license.php MIT License 
 * @version   Release: 1.0.0
 * @link      http://pear.php.net/package/Console_CommandLine
 * @since     Class available since release 0.1.0
 */
class Console_CommandLine_Action_Password extends Console_CommandLine_Action
{
    // execute() {{{

    /**
     * Execute the action with the value entered by the user.
     *
     * @param mixed $value  the option value
     * @param array $params an array of optional parameters
     *
     * @return string
     * @access public
     */
    public function execute($value=false, $params=array())
    {
        $this->setResult(empty($value) ? $this->_promptPassword() : $value);
    }
    // }}}
    // _promptPassword() {{{

    /**
     * Prompt the password to the user without echoing it.
     * XXX not echo-ing the password does not work on windows.
     *
     * @return string
     * @access private
     */
    private function _promptPassword()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            fwrite(STDOUT,
                $this->parser->message_provider->get('PASSWORD_PROMPT_ECHO'));
            @flock(STDIN, LOCK_EX);
            $passwd = fgets(STDIN);
            @flock(STDIN, LOCK_UN);
        } else {
            fwrite(STDOUT, $this->parser->message_provider->get('PASSWORD_PROMPT'));
            // disable echoing
            system('stty -echo');
            @flock(STDIN, LOCK_EX);
            $passwd = fgets(STDIN);
            @flock(STDIN, LOCK_UN);
            system('stty echo');
        }
        return trim($passwd);
    }
    // }}}
}

?>
