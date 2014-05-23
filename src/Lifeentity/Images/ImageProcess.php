<?php namespace Lifeentity\Images;

use Intervention\Image\Image;

class ImageProcess {

    /**
     * @var ImageFilter
     */
    protected $filters;

    /**
     * @var array
     */
    protected $operations = array();

    /**
     * @param ImageFilter $filters
     * @param array $operations
     */
    public function __construct(ImageFilter $filters, array $operations = array())
    {
        $this->filters = $filters;
        $this->addOperations($operations);
    }

    /**
     * @return array
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * @param $method
     * @param array $params
     */
    public function addOperation($method, $params = array())
    {
        $this->operations[$method] = $this->encodeParams($params);
    }

    /**
     * @param $operations
     */
    public function addOperations($operations)
    {
        foreach($operations as $method => $params) {

            $this->addOperation($method, $params);
        }
    }

    /**
     * @param $params
     * @return array
     */
    protected function encodeParams($params)
    {
        return array_map(function($value)
        {
            if($value === true) return 'true';

            if($value === null) return 'null';

            return $value;

        }, $params);
    }

    /**
     * @param $params
     * @return array
     */
    protected function decodeParams($params)
    {
        return array_map(function($value)
        {
            if($value === 'null') return null;

            if($value === 'true') return true;

            if(is_numeric($value)) return intval($value);

            return $value;

        }, $params);
    }

    /**
     * Run all methods on the given image
     *
     * @param \Intervention\Image\Image $image
     * @return Image
     */
    public function run(Image $image)
    {
        foreach($this->operations as $methodName => $params)
        {
            // Else if method exists in the intervention image class then use it
            if(method_exists($image, $methodName))
            {
                call_user_func_array(array($image, $methodName), $this->decodeParams($params));
            }
        }

        return $image;
    }

    /**
     * @param Image $image
     * @return \Intervention\Image\Image
     */
    public function runFilters(Image $image)
    {
        foreach($this->operations as $methodName => $params)
        {
            // Call this filter if it exists for this method name
            if($this->filters->exists($methodName))
            {
                $this->filters->call($methodName, $image, $this->decodeParams($params));
            }
        }

        return $image;
    }
}