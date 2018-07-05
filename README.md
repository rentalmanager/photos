# RentalManager - PHOTOS

A package made for Rentbits for easier maintenance and modularity of managing rental listings. 
It includes all migrations, models and relations to run the rental system.

This package uses Image Intervention library and league flysystem packages for Rackspace, SFTP and AWS access. So you do not need to install them on your app directly.

## Installation, Configuration and Usage

### Installation

Via Composer

Since this package needs to be private, first you need to add the following line to the composer.json file:

``` json
"repositories": [
        {
            "type": "git",
            "url": "git@gitlab.com:rentalmanager/photos.git"
        }
    ]
```

You must be approved by admin into the Gitlab account so your public key needs to have an access to the above repo.

After that just simply add the following line to the composer required packages

``` json
"rentalmanager/photos": "1.0.*"
```

After that run the 

``` bash
$ composer update
```

### Configuration

Once you install the package, it should be automatically discovered by the Laravel. To check this, in your terminal simply run the:


``` bash
$ php artisan
```

There you should find the all `rm:*` commands.

First step after checking is to publish the vendors:

``` bash
$ php artisan vendor:publish --tag="photos"
```
You can setup the thumbnail sizes, default disk etc  in the config file.

After that it depends on you. If you are a hard learner, run through each commands manually, but then
you can just simply run the

``` bash
$ php artisan rm:setup-photos
```

We assume you have already installed the Base package, so then just add traits automagically

```bash
$ php artisan rm:add-photoable-trait
```

Thats it...

## Usage

This package is used to store the photos in a separate table. It adds the trait to the property and unit to bind the relation as well.

### Storing the photo

You can store the photo in usual way, using Eloquent ORM.

```php 

$photo = new Photo([...]);
$photo->save();
```

When you store the photo with usual way you can associate the property like this

```php
$property->attachPhoto(1, 10); 
```

Where the first param is the ID of the photo, and the second is the ORDERING number.

or you can easily detach the photo(s) of the property

```php
$property->detachPhoto(1);

// or
$property->detachPhotos([1,2,3]);
```

Because we added the Trait to the Property and Unit model, you can fetch the photos object via

```php
$photos = $object->getOrderedPhotos();
```
The above method will return the photos by the order you gave them.

To sync the photos you will need to use the following method

```php
$object->syncPhotos($photos);
```

The `$photos` is the array of photo ID's. Please note that the order of id's will eventually be used for ordering. 
Which generally means if you provide an array `[1,3,10,2]` - photos will be displayed in this order.

## Facade

This package provides great methods for easy photo manipulation.

(First of you need to use the Facade as )

`use RentalManager\Photos\Facades\Photos;`

in your class.

You can utilize the following methods

#### Automatic image path recovery

If you are storing the external image, you'll need to get a info for storing the path and whatever.
Luckily this package provides an easy wrapper to do exactly this.

```php
$url = 'https://media.equityapartments.com/images/c_crop,x_0,y_0,w_1920,h_1080/c_fill,w_1920,h_1080/q_80/4206-28/the-kelvin-apartments-exterior.jpg';

$photoElements = Photos::parseExternalPhoto($url);

dump( $photoElements);
```

Of course you can initiate the thumbnail generator as well

```php
$photo = Photo::find(1);

$photo = Photos::generateThumbnails($photo);
```

Or if you just want to store the original image to the Storage and deal with thumbnails later on:

```php
$photo = Photos::upload($path_to_image);
```

Or if you want to upload and generate thumbnails automagically

```php
$photo = Photos::upload($path_to_image, false, true); // the last param is thumbnail generator
```

Also you can provide your path like:

```php
$photo = Photos::upload($path_to_image, 'store/there/instead');
```

Or you can generate the auto path env/properties/{random}

```php
$path = Photos::generatePath();
```

Thats it folks.