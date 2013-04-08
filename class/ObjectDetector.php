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
                        $res[] = array('x' => $x, 'y' => $y, 'w' => $size_w,
                                'h' => $size_h);
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
        return $objs;
    }
}
