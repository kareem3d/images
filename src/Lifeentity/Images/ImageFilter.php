<?php namespace Lifeentity\Images;

use Intervention\Image\Image;

class ImageFilter {

    /**
     * @var array
     */
    protected $filters = array();


    /**
     * @param $method
     * @param $filter
     */
    public function register($method, $filter)
    {
        $this->filters[$method] = $filter;
    }

    /**
     * @param $method
     * @return bool
     */
    public function exists($method)
    {
        return isset($this->filters[$method]);
    }

    /**
     * @param $method
     * @param \Intervention\Image\Image $image
     * @param array $params
     * @return mixed
     */
    public function call($method, Image $image, array $params = array())
    {
        // Add image add the first of the params array
        array_unshift($params, $image);

        return call_user_func_array($this->filters[$method], $params);
    }

} 