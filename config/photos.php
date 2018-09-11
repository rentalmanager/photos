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
    'storage_path' => 'https://s3-us-west-2.amazonaws.com/rentbits',


    /**
     * Root path for the photos
     */
    'root_path' => 'local',

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
    ]

];
