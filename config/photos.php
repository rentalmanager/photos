<?php
/**
 * Created by PhpStorm.
 * Date: 7/4/18
 * Time: 12:10 PM
 * property_images.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default disk where the images will be stored
    |--------------------------------------------------------------------------
    |
    | S3 as the default one...
    |
    */
    'default_disk' => 's3',

    /*
     * Where we are storing the photos
     */
    'storage_path' => env('AWS_URL', 'https://s3-us-west-2.amazonaws.com/rentbits'),


    /**
     * Root path for the photos
     */
    'root_path' => env('PHOTOS_ROOT_PATH', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Thumbnail sizes
    |--------------------------------------------------------------------------
    |
    | These are the thumbnail sizes which will be generated from original
    |
    */
    'thumbnail_sizes' => [
        'thumbnail' => [
            'method' => 'fit',
            'onCanvas' => false,
            'keepRatio' => false,
            'suffix' => '_16_9',
            'width' => 640,
            'height' => 360
        ],
        'thumbnail_keep_ratio' => [
            'method' => 'resize',
            'onCanvas' => true,
            'keepRatio' => true,
            'suffix' => '_16_9_canvas',
            'width' => 640,
            'height' => 360
        ],
        'small' => [
            'method' => 'fit',
            'onCanvas' => false,
            'keepRatio' => false,
            'suffix' => '_small',
            'width' => 10,
            'height' => 10,
        ],
        'large' => [
            'method' => 'resize',
            'onCanvas' => false,
            'keepRatio' => true,
            'suffix' => '_43',
            'width' => 800,
            'height' => 600,
        ],
        '3_by_1' => [
            'method' => 'fit',
            'onCanvas' => false,
            'keepRatio' => false,
            'suffix' => '_3by1',
            'width' => 940,
            'height' => 313,
        ],
        'square' => [
            'method' => 'fit',
            'onCanvas' => false,
            'keepRatio' => false,
            'suffix' => '_square',
            'width' => 200,
            'height' => 200,
            'filter' => false
        ]
    ],


    /*
      |--------------------------------------------------------------------------
      | Models
      |--------------------------------------------------------------------------
      |
      | These are the models to define the tables
      | If you want the models to be in a different namespace or
      | to have a different name, you can do it here.
      |
      */
    'models' => [
        /**
         * Photo model
         */
        'photo' => 'App\RentalManager\AddOns\Photo'
    ],

    /*
     |--------------------------------------------------------------------------
     | Tables
     |--------------------------------------------------------------------------
     |
     | These are the tables to store all the necessary data.
     |
     */
    'tables' => [
        /**
         *  Photos table
         */
        'photos' => 'photos',
        /**
         * Intermediate table
         */
        'photo_nodes' => 'photo_nodes'
    ],

    /*
  |--------------------------------------------------------------------------
  | Foreign Keys
  |--------------------------------------------------------------------------
  |
  | These are the foreign keys used by propeller in the intermediate tables.
  |
  */
    'foreign_keys' => [
        /**
         * Photo foreign key
         */
        'photo' => 'photo_id'
    ]


];
