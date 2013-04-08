<?php

include("ImageException.php");

/**
 * Object modeling of an image
 * 
 * @author Benjamin Georgeault & Jéméry Papin
 *
 */
class Image {

    /**
     * Use to resize image to minify operation on image
     * 
     * @var integer
     */
    const max_dim = 512;

    /**
     * Image's type (JPEG, PNG, etc...)
     * 
     * @var string
     */
    private $type;

    /**
     * File path of the image
     *
     * @var string
     */
    private $file_path;

    /**
     * Ratio of the image W/H
     * 
     * @var float
     */
    private $size_ratio;

    /**
     * GD ressource
     * 
     * @var ressource
     */
    private $or = null;

    /**
     * Height of the image
     * 
     * @var integer
     */
    private $oh;

    /**
     * Width of the image
     * 
     * @var integer
     */
    private $ow;

    /**
     * GD ressource of the thumbnail
     *
     * @var ressource
     */
    private $tr = null;

    /**
     * Resize factor
     * 
     * @var float
     */
    private $t_facteur;

    /**
     * Height of the thumbnail
     *
     * @var integer
     */
    private $th;

    /**
     * Width of the thumbnail
     *
     * @var integer
     */
    private $tw;

    /**
     * GD ressource of the gray thumbnail
     *
     * @var ressource
     */
    private $gtr = null;

    /**
     * Integral image
     * 
     * @var array:number
     */
    private $integral = null;

    /**
     * Integral square image
     *
     * @var array:number
     */
    private $integralSquare = null;

    /**
     * Creates an Image instance for a specified image. 
     * 
     * @param string $file Path to the image
     */
    function __construct($file_path) {
        $this->file_path = $file_path;

        $this->checkValidFileImage();
        $this->loadImage();
        $this->initStats();
    }

    /**
     * Destructor
     */
    function __destruct() {
        if (is_resource($this->tr)) {
            imagedestroy($this->tr);
        }

        if (is_resource($this->or))
            imagedestroy($this->or);

        if (is_resource($this->gtr))
            imagedestroy($this->gtr);
    }

    /**
     * Check if the file is a valid image file.
     * 
     * @throws ImageException
     */
    private function checkValidFileImage() {
        if (!is_file($this->file_path))
            throw new ImageException("This is not a valide file.");

        // get image type
        $this->type = exif_imagetype($this->file_path);
        if ($this->type == false)
            throw new ImageException("This is not a valide image.");
    }

    /**
     * Load image from a file
     * 
     * @throws ImageException
     */
    private function loadImage() {
        switch ($this->type) {
        case IMAGETYPE_GIF:
            $this->or = imagecreatefromgif($this->file_path);
            break;
        case IMAGETYPE_JPEG:
            $this->or = imagecreatefromjpeg($this->file_path);
            break;
        case IMAGETYPE_PNG:
            $this->or = imagecreatefrompng($this->file_path);
            break;
        default:
            throw new ImageException("Image not recognized.");
        }

        if ($this->or == false)
            throw new ImageException("Error load image.");
    }

    /**
     * Init stats of the image (width, height, etc...) 
     */
    private function initStats() {
        // get image sizes
        list($this->ow, $this->oh) = getimagesize($this->file_path);

        $this->size_ratio = $this->ow / $this->oh;
    }

    /**
     * Thumbnailize the image
     * 
     * @throws ImageException
     */
    private function thumbnailize() {
        if ($this->size_ratio > 1 && $this->ow > self::max_dim) {
            $this->tw = self::max_dim;
            $this->th = (int) (self::max_dim / $this->size_ratio);
            $this->t_facteur = $this->tw / self::max_dim;
        } else if ($this->size_ratio < 1 && $this->oh > self::max_dim) {
            $this->tw = (int) (self::max_dim * $this->size_ratio);
            $this->th = self::max_dim;
            $this->t_facteur = $this->th / self::max_dim;
        } else {
            $this->tw = $this->ow;
            $this->th = $this->oh;
            $this->t_facteur = 1;
        }

        $this->tr = imagecreatetruecolor($this->tw, $this->th);
        if (!imagecopyresampled($this->tr, $this->or, 0, 0, 0, 0, $this->tw,
                $this->th, $this->ow, $this->oh))
            throw new ImageException("Error thumbnailize.");
    }

    /**
     * Create a gray thumbnail
     * 
     * @throws ImageException
     */
    private function gray() {
        $this->getThumb();

        $this->gtr = imagecreatetruecolor($this->tw, $this->th);
        if (!imagecopyresampled($this->gtr, $this->tr, 0, 0, 0, 0, $this->tw,
                $this->th, $this->tw, $this->th))
            throw new ImageException("Error gray.");

        imagefilter($this->gtr, IMG_FILTER_GRAYSCALE);
    }

    /**
     * Create integral images
     */
    private function initIntegral() {
        $this->getGray();

        // init tabs
        $this->integral = array_fill(0, $this->tw,
                array_fill(0, $this->th, null));
        $this->integralSquare = array_fill(0, $this->tw,
                array_fill(0, $this->th, null));

        for ($x = 0; $x < $this->tw; $x++) {
            $cumul = 0;
            $cumul_s = 0;
            for ($y = 0; $y < $this->height; $y++) {
                $colors = imagecolorsforindex($this->image,
                        imagecolorat($this->image, $x, $y));

                // color to grayscale
                $value = 0.299 * $colors['red'] + 0.587 * $colors['green']
                        + 0.114 * $colors['blue'];
                $value_s = $value * $value;

                // calcul cumul
                $cumul += $value;
                $cumul_s += $value_s;

                // set in integral images
                $this->integral[$x][$y] = ($x > 0 ? $this->integral[$x - 1][$y]
                        : 0) + $cumul;
                $this->integralSquare[$x][$y] = ($x > 0 ? $this
                                ->integralSquare[$x - 1][$y] : 0) + $cumul_s;
            }
        }
    }

    /**
     * Print to screen the image
     *
     * @param $ext see image_type_to_mime_type()
     * @param $type Choose of image type
     * @throws ImageException
     */
    public function toImage($ext = IMAGETYPE_PNG, $type = 'img') {
        switch ($type) {
        case 'img':
            $img = $this->or;
            break;
        case 'thumb':
            $img = $this->getThumb();
            break;
        case 'gray':
            $img = $this->getGray();
            break;
        default:
            throw new ImageException("Impossible to show the image.");
        }

        switch ($ext) {
        case IMAGETYPE_GIF:
            header('Content-Type: image/gif');
            imagegif($img);
            break;
        case IMAGETYPE_JPEG:
            header('Content-Type: image/jpeg');
            imagejpeg($img);
            break;
        case IMAGETYPE_PNG:
            header('Content-Type: image/png');
            imagepng($img);
            break;
        default:
            throw new ImageException("Impossible to show the image.");
        }
    }

    /**
     * Get thumbnail GD ressource
     *
     * @return ressource
     */
    public function getThumb() {
        if ($this->tr == null)
            $this->thumbnailize();

        return $this->tr;
    }

    /**
     * Get gray thumbnail GD ressource
     *
     * @return ressource
     */
    public function getGray() {
        if ($this->gtr == null)
            $this->gray();

        return $this->gtr;
    }

    /**
     * Get image GD ressource
     *
     * @return ressource
     */
    public function getImage() {
        return $this->or;
    }

    /**
     * Get height of thumbnail
     * 
     * @return number
     */
    public function getHeightT() {
        if ($this->tr == null)
            $this->thumbnailize();

        return $this->th;
    }

    /**
     * Get width of thumbnail
     *
     * @return number
     */
    public function getWidthT() {
        if ($this->tr == null)
            $this->thumbnailize();

        return $this->tw;
    }

    /**
     * Get height of image
     *
     * @return number
     */
    public function getHeight() {
        return $this->oh;
    }

    /**
     * Get width of image
     *
     * @return number
     */
    public function getWidth() {
        return $this->ow;
    }

    /**
     * get ratio between thumbnail and image
     * 
     * @return number
     */
    public function getRatio() {
        return $this->size_ratio;
    }
    
    /**
     * Get integral image
     * 
     * @return array:number
     */
    public function getIntegral(){
        if($this->integral == null)
            $this->initIntegral();
        
        return $this->integral;
    }
    
    /**
    * Get integral square image
    *
    * @return array:number
    */
    public function getIntegralSquare(){
        if($this->integralSquare == null)
            $this->initIntegral();
    
        return $this->integralSquare;
    }
}

