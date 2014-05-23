<?php namespace Lifeentity\Images;

use Lifeentity\Images\Cryption\ProcessCryptifierInterface;

class ImageFilename {

    /**
     * @var Cryption\ProcessCryptifierInterface
     */
    protected $cryptifier;

    /**
     * @param ProcessCryptifierInterface $cryptifier
     */
    public function __construct(ProcessCryptifierInterface $cryptifier)
    {
        $this->cryptifier = $cryptifier;
    }

    /**
     * Disassemble the cache file name to process and filename
     *
     * @param $cached
     * @return array (ImageProcess, filename)
     */
    public function disassemble($cached)
    {
        $encryptedProcess = substr($cached, 0, strrpos($cached, '_'));

        $filename = substr($cached, strrpos($cached, '_') + 1);

        return array($this->cryptifier->decrypt($encryptedProcess), $filename);
    }

    /**
     * Return the cached url.
     *
     * @param ImageProcess $imageProcess
     * @param $filename
     * @return string
     */
    public function cached(ImageProcess $imageProcess, $filename)
    {
        return $this->cryptifier->encrypt($imageProcess).'_'.$filename;
    }

} 