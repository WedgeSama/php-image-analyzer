<?php
/*
 * This file is part of the Phia package.
 *
 * (c) Benjamin Georgeault <https://github.com/WedgeSama/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phia\Exception;

/**
 * Exception class for configuration.
 * 
 * @author Benjamin Georgeault <wedgesama@gmail.com>
 */
class UnexpectedConfigException extends \Exception {

    public function __construct($option) {
        if (is_array($allowed))
            $allowed = implode(', ', $allowed);
        
        $msg = sprintf('Unexpected option "%s".', $option);
        parent::__construct($msg);
    }

}
