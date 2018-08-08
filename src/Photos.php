<?php
namespace RentalManager\Photos;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * Created by PhpStorm.
 * Date: 7/4/18
 * Time: 12:13 PM
 * Images.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */


class Photos
{
    /**
     * Laravel application.
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;


    /**
     * Base constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }


    /**
     * Parse the data out of the photo URL
     *
     * @param $url
     * @return object
     */
    public function parseExternalPhoto($url)
    {
        $parsed = parse_url($url);

        $fileExtension = pathinfo(basename($parsed['path']), PATHINFO_EXTENSION);
        $fileName = basename($parsed['path']);

        switch ( $fileExtension )
        {
            case 'jpg':
                $fileType = 'image/jpeg';
                break;

            case 'jpeg':
                $fileType = 'image/jpeg';
                break;

            case 'png':
                $fileType = 'image/png';
                break;

            default:
                $fileType = 'image/jpeg';
                break;
        }

        return (object) [
            'external_url' => $parsed['scheme'] . '://' . $parsed['host'],
            'path' => trim( str_replace($fileName, '', $parsed['path']), '/'),
            'file_name' => str_replace('.' . $fileExtension, '', basename($parsed['path'])),
            'file_extension' => $fileExtension,
            'file_type' => $fileType
        ];
    }


    /**
     * Upload
     *
     * @param $photo
     * @param $path
     * @return mixed
     */
    public function upload($photo, $path = 'local')
    {
        $path = $this->generatePath($path);

        $filename = md5( $photo->file_name ) . time();

        // use the image intervention to encode and orientate the image according to the Exif data
        $image = Image::make( $photo->sources['original'])->orientate()->encode('jpg');

        // put into the storage
        Storage::disk('s3')->put( $path . '/' . $filename . '.jpg', $image->__toString(), 'public');

        // Ok now we need to add this item to the database
        $photo->disk = 's3';
        $photo->is_external = false;
        $photo->external_url = null;
        $photo->path = $path;
        $photo->has_thumbnails = false;
        $photo->file_type = 'image/jpeg';
        $photo->file_name = $filename;
        $photo->file_extension = 'jpg';
        $photo->save();

        return $photo;
    }

    /**
     * Generate the unique path
     *
     * @param $path
     * @param $id
     * @return string
     */
    public function generatePath($path = null, $id = false)
    {
        $id = ( $id ) ? $id : rand() . uniqid();
        return Config::get('photos.root_path') . '/properties/' . $id;
    }

    /**
     * Generate the thumbnails for a photo
     *
     * @param $photo
     * @return mixed
     */
    public function generateThumbnails( $photo )
    {
        // return if we have the thumbnails or is external
        if ( $photo->has_thumbnails )
        {
            return $photo;
        }

        $thumbnail_sizes = null;
        $path = $photo->path;
        $filename = $photo->file_name;

        foreach ( Config::get('photos.thumbnail_sizes') as $key => $size )
        {
            // Create the image, orientate and encode it as jpg ALWAYS
            $image = Image::make( $photo->sources['original'])->orientate()->encode('jpg');

            // Get the method
            $method = $size['method'];

            // If keep ratio
            if ( $size['keepRatio'] )
            {
                switch ( $method )
                {
                    case 'fit':
                        // add callback functionality to retain maximal original image size
                        $image->fit( $size['width'], $size['height'], function( $constraint ) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        } );
                        break;

                    case 'resize':
                        // add callback functionality to retain maximal original image size
                        $image->resize( $size['width'], $size['height'], function( $constraint ) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        } );
                        break;
                }

            } else {
                // Just do the method
                switch ( $method )
                {
                    case 'fit':
                        $image->fit( $size['width'], $size['height'] );
                        break;

                    case 'resize':
                        $image->resize($size['width'], $size['height']);
                        break;
                }
            }


            // If we need to place the image on canvas
            if ( $size['onCanvas'] )
            {
                // create new image with transparent background color
                $background = Image::canvas($size['width'], $size['height'], '#000000');

                // insert resized image centered into background
                $background->insert($image, 'center');

                // Stream
                $background->stream();

                // Filename
                $toString = $background->__toString();
            } else {
                $image->stream();

                // Filename
                $toString = $image->__toString();
            }

            // Upload
            Storage::disk('s3')->put( $path . '/' . $filename . $size['suffix'] . '.jpg', $toString, 'public');

            // And now update the thumbnail size which is done
            $thumbnail_sizes[$key] = $filename . $size['suffix'];
        }

        // Update the database
        $photo->has_thumbnails = true;
        $photo->thumbnails = $thumbnail_sizes;
        $photo->save();

        return $photo;
    }



}
