<?php
include("Image.php");
include("Haar/Loader.php");

class ObjectDetector {
    /**
     * Ondelette start number
     * 
     * @var number
     */
    const onde_base = 2;

    /**
     * Ondelette increment
     * 
     * @var number
     */
    const onde_inc = 1.25;

    /**
     * Pixel increment
     * 
     * @var number
     */
    const increment = 0.1;

    /**
     * Min number of neighbors
     * 
     * @var int
     */
    const min_neighbors = 3;

    /**
     * Haar loader
     * 
     * @var Haar/Loader
     */
    private $loader;

    function __construct(Loader $loader) {
        $this->loader = $loader;

    }

    /**
     * Get squares of object in image
     * 
     * @param Image $img
     * @return array
     */
    public function detectObject(Image $img) {
        $res = array();

        // max ondelette
        $onde_max = min($img->getWidthT() / $this->loader->width,
                $img->getHeightT() / $this->loader->height);

        // ondelette loop
        for ($onde = self::onde_base; $onde < $onde_max; $onde *= self::onde_inc) {
            $var = $onde * $this->loader->width;
            $pas_w = (int) ($var * self::increment);
            $size_w = (int) $var;

            $var = $onde * $this->loader->height;
            $pas_h = (int) ($var * self::increment);
            $size_h = (int) $var;

            // X axe loop
            for ($x = 0; $x < $img->getWidthT() - $size_w; $x += $pas_w) {
                // Y axe loop
                for ($y = 0; $y < $img->getHeightT() - $size_h; $y += $pas_h) {
                    $detect_ok = true;
                    // stages loop
                    foreach ($this->loader->stages as $s) {
                        if (!$s->valideStage($img, $x, $y, $onde)) {
                            $detect_ok = false;
                            break;
                        }
                    }
                    // add object detected
                    if ($detect_ok)
                        $res[] = array(
                                'x' => $x, 'y' => $y, 'w' => $size_w,
                                'h' => $size_h
                        );
                }
            }
        }

        return $this->compare($res);
    }

    /**
     * Delete duplicate detect objects, implement later...
     * 
     * @param array $objs
     * @return array
     */
    private function compare($objs) {
        $res = array();
        $temp = array();
        $nb_classes = 0;

        for ($i = 0; $i < count($objs); $i++) {
            $found = false;
            for ($j = 0; $j < $i; $j++) {
                if ($this->equal($objs[$j], $objs[$i])) {
                    $found = true;
                    $temp[$i] = $temp[$j];
                }
            }

            if (!$found) {
                $temp[$i] = $nb_classes;
                $nb_classes++;
            }
        }

        $neighbors = array();
        $rect = array();
        for ($i = 0; $i < $nb_classes; $i++) {
            $neighbors[$i] = 0;
            $rect[$i] = array(
                'x' => 0, 'y' => 0, 'w' => 0, 'h' => 0
            );
        }

        for ($i = 0; $i < count($objs); $i++) {
            $neighbors[$temp[$i]]++;
            $rect[$temp[$i]]['x'] += $objs[$i]['x'];
            $rect[$temp[$i]]['y'] += $objs[$i]['y'];
            $rect[$temp[$i]]['w'] += $objs[$i]['w'];
            $rect[$temp[$i]]['h'] += $objs[$i]['h'];
        }

        for ($i = 0; $i < $nb_classes; $i++) {
            $n = $neighbors[$i];
            if ($n >= self::min_neighbors) {
                $r = array(
                    'x' => 0, 'y' => 0, 'w' => 0, 'h' => 0
                );
                $r['x'] = ($rect[$i]['x'] * 2 + $n) / (2 * $n);
                $r['y'] = ($rect[$i]['y'] * 2 + $n) / (2 * $n);
                $r['w'] = ($rect[$i]['w'] * 2 + $n) / (2 * $n);
                $r['h'] = ($rect[$i]['h'] * 2 + $n) / (2 * $n);

                $res[] = $r;
            }
        }
        return $res;
    }

    /**
     * Check if rect are equivalent
     * 
     * @param array $r1
     * @param array $r2
     * @return boolean
     */
    private function equal($r1, $r2) {
        $dist = (int) ($r1['w'] * 0.2);

        if (($r2['x'] <= $r1['x'] + $dist && $r2['x'] >= $r1['x'] - $dist
                && $r2['y'] <= $r1['y'] + $dist && $r2['y'] >= $r1['y'] - $dist
                && $r2['w'] <= (int) ($r1['w'] * 1.2)
                && (int) ($r2['w'] * 1.2) >= $r1['w'])
                || ($r1['x'] >= $r2['x']
                        && $r1['x'] + $r1['w'] <= $r2['x'] + $r2['w']
                        && $r1['y'] >= $r2['y']
                        && $r1['y'] + $r1['h'] <= $r2['y'] + $r2['h']))
            return true;

        return false;
    }
}
