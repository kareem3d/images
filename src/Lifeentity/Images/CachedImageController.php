<?php namespace Lifeentity\Images;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;

class CachedImageController extends Controller {

    /**
     * @param ImageFilename $imageFilename
     */
    public function __construct(ImageFilename $imageFilename)
    {
        $this->imageFilename = $imageFilename;
    }

    /**
     * Create the required image..
     *
     * @param $path
     * @param $cachedImages
     * @return mixed
     */
    public function display($path, $cachedImages)
    {
        // Images full path
        $imagesPath = Config::get('images::config.base').'/'.Config::get('images::config.images_dir').'/'.$path;

        // Disassemble the cached image to ImageProcess and filename
        list($imageProcess, $filename) = $this->imageFilename->disassemble($cachedImages);

        // make new intervention image from the original source
        $image = Image::make($imagesPath.'/'.$filename);

        // Run operations on the original image
        $imageProcess->run($image);

        // Run filters on the same image
        $imageProcess->runFilters($image);

        // If no-cache input exists then escape the cache process
        // otherwise save this image in the cache
        if(! Input::has('no-cache')) {

            //
            $cacheFullPath = $imagesPath.'/'.Config::get('images::config.cache_dir');

            //
            if(! file_exists($cacheFullPath)) mkdir($cacheFullPath);

            // Save the image in the same path which represents the cache path
            $image->save($cacheFullPath.'/'.$cachedImages);
        }

        return $image->response();
    }
} 