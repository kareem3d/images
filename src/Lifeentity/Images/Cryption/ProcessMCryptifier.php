<?php namespace Lifeentity\Images\Cryption;

use Illuminate\Support\Facades\App;
use Lifeentity\Images\ImageProcess;

class ProcessMCryptifier implements ProcessCryptifierInterface {

    /**
     * @var string
     */
    protected $key;

    /**
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Encode image process.
     *
     * @param ImageProcess $process
     * @return string
     */
    public function encrypt(ImageProcess $process)
    {
        $str = $this->convertToString($process);
        $block = mcrypt_get_block_size('rijndael_128', 'ecb');
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        return strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $str, MCRYPT_MODE_ECB)), '+/=', '-_,');
    }

    /**
     * @param string $str
     * @return ImageProcess
     */
    public function decrypt($str)
    {
        $str = base64_decode(strtr($str, '-_,', '+/='));
        $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $str, MCRYPT_MODE_ECB);
        $len = strlen($str);
        $pad = ord($str[$len-1]);
        return $this->makeFromString(substr($str, 0, strlen($str) - $pad));
    }

    /**
     * Create new image process from string
     *
     * @param $string
     * @return ImageProcess
     */
    protected function makeFromString( $string )
    {
        $operations = array();

        $methods = explode('_', $string);

        foreach($methods as $method)
        {
            $pieces = explode('-', $method);

            $key = array_shift($pieces);

            $operations[$key] = $pieces;
        }

        // Image process
        return App::make('Lifeentity\Images\ImageProcess', $operations);
    }

    /**
     * Convert image process to string
     *
     * @param ImageProcess $imageProcess
     * @return string
     */
    protected function convertToString(ImageProcess $imageProcess)
    {
        $string = '';

        $operations = $imageProcess->getOperations();

        // Sort operations first
        ksort($operations);

        foreach($operations as $key => $value)
        {
            $string .= rtrim($key.'-'.implode('-', $value), '-') . '_';
        }

        return rtrim($string, '_');
    }
}