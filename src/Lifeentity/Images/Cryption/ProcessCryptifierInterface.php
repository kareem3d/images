<?php namespace Lifeentity\Images\Cryption;

use Lifeentity\Images\ImageProcess;

interface ProcessCryptifierInterface {

    /**
     * Encrypt image process
     * @param  ImageProcess $image 
     * @return string              
     */
    public function encrypt(ImageProcess $image);

    /**
     * Decrypt the given message to an image process
     * @param  string $message
     * @return ImageProcess
     */
    public function decrypt($message);

}