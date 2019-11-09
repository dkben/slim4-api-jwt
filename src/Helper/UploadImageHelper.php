<?php


namespace App\Helper;


use Gregwar\Image\Image;
use Mimey\MimeTypes;

class UploadImageHelper
{
    private $accept;

    private $pathOrigin;
    private $pathBig;
    private $pathMiddle;
    private $pathSmall;
    private $imageSize;

    public function __construct($location = 'public')
    {
        $this->accept = $GLOBALS['systemConfig']['upload']['acceptImage'];
        $this->imageSize = $GLOBALS['systemConfig']['upload']['imageSize'];
        $basePath = $GLOBALS['systemConfig']['upload'][$location] . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        $this->pathOrigin = $basePath . '/o/';
        $this->pathBig = $basePath . '/b/';
        $this->pathMiddle = $basePath . '/m/';
        $this->pathSmall = $basePath . '/s/';

        if (!file_exists($this->pathOrigin)) {
            mkdir($this->pathOrigin, 0755, true);
        }

        if (!file_exists($this->pathBig)) {
            mkdir($this->pathBig, 0755, true);
        }

        if (!file_exists($this->pathMiddle)) {
            mkdir($this->pathMiddle, 0755, true);
        }

        if (!file_exists($this->pathSmall)) {
            mkdir($this->pathSmall, 0755, true);
        }
    }

    public function upload()
    {
        $basename = bin2hex(random_bytes(8));
        $tmpName = $this->pathOrigin . $basename;
        file_put_contents($tmpName, file_get_contents('php://input'));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        $mimes = new MimeTypes;
        $extension = $mimes->getExtension($mimeType);
        $newName = sprintf('%s.%0.8s', $basename, $extension);

        if (in_array($extension, $this->accept)) {
            rename($tmpName, $this->pathOrigin . $newName);
            // 縮圖
            Image::open($this->pathOrigin . $newName)
                ->cropResize($this->imageSize['big'][0], $this->imageSize['big'][1])
                ->save($this->pathBig . $newName);

            Image::open($this->pathOrigin . $newName)
                ->cropResize($this->imageSize['middle'][0], $this->imageSize['middle'][1])
                ->save($this->pathMiddle . $newName);

            Image::open($this->pathOrigin . $newName)
                ->cropResize($this->imageSize['small'][0], $this->imageSize['small'][1])
                ->save($this->pathSmall . $newName);

            $message = 'success';
        } else {
            unlink($tmpName);
            $message = 'failed';
        }

        return $message;
    }

}