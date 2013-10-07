<?php
/*
 * This file is part of the Phia package.
 *
 * (c) Benjamin Georgeault <https://github.com/WedgeSama/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once __DIR__ . '/../lib/Phia/Autoloader.php';

use Phia\Autoloader;
use Phia\Phia;

$loader = new Autoloader();
$loader->register();

$phia = new Phia();
