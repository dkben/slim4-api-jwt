<?php


namespace App\Helper;


use Mimey\MimeTypes;

class UploadFileHelper
{
    private $accept;

    public function __construct()
    {
        $this->accept = ['jpg', 'jpeg', 'png'];
    }

    public function upload()
    {
        $path = $GLOBALS['systemConfig']['upload']['public'];
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $basename = date('Ymd') . '-' . bin2hex(random_bytes(8));
        $tmpName = $path . $basename;
        file_put_contents($tmpName, file_get_contents('php://input'));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        $mimes = new MimeTypes;
        $extension = $mimes->getExtension($mimeType);
        $newName = $path . sprintf('%s.%0.8s', $basename, $extension);

        if (in_array($extension, $this->accept)) {
            rename($tmpName, $newName);
            $message = 'success';
        } else {
            unlink($tmpName);
            $message = 'failed';
        }

        // TODO 如果是圖片，需要縮圖

        return $message;
    }

}