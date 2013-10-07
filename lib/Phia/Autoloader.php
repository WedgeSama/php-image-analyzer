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
 * Autoloads Phia classes.
 *
 * @author Benjamin Georgeault <wedgesama@gmail.com>
 */
class Autoloader {

    /**
     * Register the autoloader to the spl.
     */
    public static function register() {
        spl_autoload_register(
                array(
                        new self(), 
                        'autoloader' 
                ));
    }

    /**
     * Autoload classes.
     * 
     * @param string $className
     */
    public static function autoloader($className) {
        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';
        
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) .
                     DIRECTORY_SEPARATOR;
        }
        
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                 $fileName . str_replace('_', DIRECTORY_SEPARATOR, $className) .
                 '.php';
        
        require $fileName;
    }

}