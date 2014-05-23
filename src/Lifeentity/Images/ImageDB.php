<?php namespace Lifeentity\Images;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class ImageDB extends Model {

    /**
     * @var string
     */
    protected $table = 'le_images';

    /**
     * @var array
     */
    protected $fillable = array('name', 'path', 'position', 'imageable_type', 'imageable_id');

    /**
     * @var ImageFilename
     */
    protected $imageFilename;

    /**
     * Setup event bindings
     */
    public static function boot()
    {
        parent::boot();

        // When creating a new image first set order
        static::creating(function(ImageDB $image)
        {
            $image->order = $image->getLastOrder() + 1;
        });
    }

    /**
     * Exchange orders and save them to database
     *
     * @param ImageDB $image
     */
    public function exchangeOrder( ImageDB $image )
    {
        $tempOrder = $image->order;

        $image->order = $this->order;
        $this->order  = $tempOrder;

        $image->save();
        $this->save();
    }

    /**
     * This will override the given image which means it will be deleted
     * from database...
     *
     * @param Model $model
     */
    public function override( Model $model)
    {
        $this->order = $model->order;

        $model->delete();
    }

    /**
     * Check if there's another image with the same order.
     *
     * @return mixed
     */
    public function orderExists()
    {
        return $this->getOrderGroup()
            ->where('order', $this->order)
            ->where('id', '!=', $this->id)->count() > 0;
    }

    /**
     * @return int
     */
    public function getLastOrder()
    {
        return $this->getOrderGroup()->max('order');
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getOrderGroup()
    {
        return $this->where('imageable_type', $this->imageable_type)
                    ->where('name', $this->name);
    }

    /**
     * @return ImageFilename
     */
    public function getImageFilename()
    {
        return $this->imageFilename ?: $this->imageFilename = App::make('Lifeentity\Images\ImageFilename');
    }

    /**
     * @return ImageProcess
     */
    public function getImageProcess()
    {
        return $this->imageProcess ?: $this->imageProcess = App::make('Lifeentity\Images\ImageProcess');
    }

    /**
     * Get cached url
     */
    public function getCachedUrlAttribute()
    {
        return $this->dirname.'/gen/'.$this->getImageFilename()->cached($this->getImageProcess(), $this->basename);
    }

    /**
     * @return string
     */
    public function getPathAttribute()
    {
        return trim($this->attributes['path'], '/');
    }
    
    /**
     * @return string
     */
    public function getOriginalUrlAttribute()
    {
        return URL::to($this->path);
    }

    /**
     * Get filename 
     * @return string 
     */
    public function getBasenameAttribute()
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    /**
     * Get dir name
     * @return string
     */
    public function getDirnameAttribute()
    {
        return pathinfo($this->path, PATHINFO_DIRNAME);
    }

    /**
     * @return $this
     */
    public function addOperation()
    {
        $args = func_get_args();

        $this->getImageProcess()->addOperation(array_shift($args), $args);

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}