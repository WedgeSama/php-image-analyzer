<?php
/*
 * This file is part of the Phia package.
 *
 * (c) Benjamin Georgeault <https://github.com/WedgeSama/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phia\GMagick;

use Phia\Image as ImageBase;

/**
 * Object to represent an image. Works with GMagick.
 * 
 * @author Benjamin Georgeault <wedgesama@gmail.com>
 */
class Image extends ImageBase {

    public function __construct($fileName) {
        parent::__construct($fileName);
    }

}
