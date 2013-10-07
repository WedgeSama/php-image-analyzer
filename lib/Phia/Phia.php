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

use Phia\Exception\InvalidLoadException;
use Phia\Exception\AlreadyLoadException;
use Phia\Exception\ConfigException;
use Phia\Exception\UnexpectedConfigException;

/**
 * Main class of Phia library.
 * 
 * @author Benjamin Georgeault <wedgesama@gmail.com>
 */
class Phia {

    /**
     * The default Phia configuration.
     *
     * @var array
     */
    private static $configDefault = array(
            'engine' => 'GD' 
    );

    /**
     * The Phia configuration.
     * 
     * @var array
     */
    private $config;

    /**
     * Images to proccess.
     * 
     * @var \Phia\Image[]
     */
    private $images;

    /**
     * Init Phia with the given configuration.
     * 
     * @param array $config
     */
    public function __construct(array $config = array()) {
        $engines = array(
                'GD', 
                'ImageMagick', 
                'GMagick' 
        );
        
        // valid config
        foreach ($config as $option => $value) {
            switch ($option) {
                case 'engine':
                    if (! in_array($value, $engines))
                        throw new ConfigException($option, $value, $engines);
                    break;
                default:
                    throw new UnexpectedConfigException($option);
            }
        }
        
        $this->config = array_merge(self::$configDefault, $config);
        
        // Optimize config
        $this->config['engine_class'] = 'Phia\\' .
                 $this->config['engine'] . '\\Image';
        
        // init vars
        $this->images = array();
    }

    /**
     * Add one image to Phia.
     *
     * @param string $fileName
     * @return \Phia\Phia
     */
    public function addImage($fileName) {
        if (! $this->checkFile($fileName))
            throw new InvalidLoadException($fileName);
        
        if (array_key_exists($fileName, $this->images))
            throw new AlreadyLoadException($fileName);
        
        $this->images[] = new $this->config['engine_class']($fileName);
        
        return $this;
    }

    /**
     * Add multiple images to Phia.
     *
     * @param array $fileNames
     * @return \Phia\Phia
     */
    public function addImages(array $fileNames) {
        foreach ($fileNames as $fileName)
            $this->addImage($fileName);
        
        return $this;
    }

    /**
     * Add images using a patern to find files.
     * 
     * @param string $patern
     * @return \Phia\Phia
     */
    public function loadImagesFromPatern($patern) {
        // NOT IMPLEMENTED
        return $this;
    }

    /**
     * Check if $fileName exist.
     * Can be a local or remote file/dir.
     *
     * @param string $fileName
     * @return boolean
     */
    private function checkFile($fileName) {
        // NOT IMPLEMENTED
        return true;
    }

}