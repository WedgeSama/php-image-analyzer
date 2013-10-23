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
 * Exception class for loading an image.
 * 
 * @author Benjamin Georgeault <wedgesama@gmail.com>
 */
class AlreadyLoadException extends \Exception {

    public function __construct($fileName) {
        $msg = sprintf('The image "%s" is already load.', $fileName);
        parent::__construct($msg);
    }

}
