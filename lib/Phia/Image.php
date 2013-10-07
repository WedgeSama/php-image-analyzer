<?php
/*
 * This file is part of the Phia package.
 *
 * (c) Benjamin Georgeault <https://github.com/WedgeSama/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Phia;

/**
 * Object to represente an image. Base for all images PHP extension.
 * 
 * @author Benjamin Georgeault <wedgesama@gmail.com>
 */
abstract class Image {

    /**
     * The file name of the image.
     * 
     * @var string
     */
    protected $fileName;

    public function __construct($fileName) {
        $this->fileName = $fileName;
    }

}