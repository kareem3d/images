# Image generation and caching in Laravel

This Laravel 4 package provides an easy way to generate and cache images on the fly


## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `lifeentity/images`.

    "require": {
        "lifeentity/images": "2.*"
    }

Next, update Composer from the Terminal:

    composer update

Once this operation completes, the final step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

    'Lifeentity\Images\ImagesServiceProvider'

Next, run this migrate command to create the images table (don't forget to configure your database)

    php artisan migrate --package=lifeentity/images

Optional step, run this artisan command to publish the config file

    php artisan config:publish lifeentity/images

The config file contains information about:

- your images directory
- the base path
- the cache directory name

## Usage

Attach any of your models to the image eloquent model.

```php
<?php

use Lifeentity\Images\ImageDB;

class Product {
    // ...
    protected function images() {
        return $this->morphMany('Lifeentity\Images\ImageDB', 'imageable');
    }
}
```

Create new image and save it to the product

```php
<?php
// Create a new image and attach it to a product
$image = new ImageDB(array(
    'path' => '/images/path/to/image/image.jpg'
));

Product::find(1)->image()->save($image);
```

Display product original image and resized version of the image

```php
<?php
$image = Product::find(1);
echo '<img src="'.$image->original_url.'" />';

// Resize image before displaying to the user
echo '<img src="'.$image->addOperation('resize', 300, null, true)->cached_url.'" />';
```

Want to make complex operations on the image? Register an image filter

```php
<?php
// Register new operation
App::make('Lifeentity\Images\ImageFilter')->register('watermark.v1', function(Intervention\Image\Image $image)
{
    // If you want you can add this facade to the aliases in the app.php config file
    $watermark = \Intervention\Image\Facades\Image::make(public_path('/images/watermark.jpg'));

    // For a list of operations you can do on an image please refer to intervention image package bellow
    $watermark->resize(0.4 * $image->getWidth(), null, function($constraints)
    {
        $constraints->aspectRatio();
    });

    $image->insert($watermark, 'bottom-right');
});
```

This package depends on intervention/image v2.*.
For list of operations you can do on images follow this link [Intervention Image](https://github.com/Intervention/image)