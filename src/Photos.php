<?php
namespace RentalManager\Photos;

use App\RentalManager\AddOns\Photo;
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
     * @param $file
     * @param $path
     * @param bool $createThumbnails
     * @return mixed
     */
    public function upload($file, $path = false, $createThumbnails = false)
    {
        $path = ( !$path ) ? $this->generatePath() : $path;

        // generate the unique filename
        $filename = md5( $file ) . time();

        // use the image intervention to encode and orientate the image according to the Exif data
        $image = Image::make($file)->orientate()->encode('jpg');

        // put into the storage
        Storage::disk('s3')->put( $path . '/' . $filename . '.jpg', $image->__toString(), 'public');

        // Ok now we need to add this item to the database
        $photo = Photo::create([
            'disk' => 's3',
            'is_external' => false,
            'path' => $path,
            'has_thumbnails' => false,
            'file_type' => 'image/jpeg',
            'file_name' => $filename,
            'file_extension' => 'jpg'
        ]);

        if ( $createThumbnails )
        {
            $this->generateThumbnails($photo);
        }
        return $photo;
    }

    /**
     * Generate the unique path
     *
     * @return string
     */
    public function generatePath()
    {
        return env('APP_ENV') . '/properties/' . rand() . uniqid();
    }

    /**
     * Generate the thumbnails for a photo
     *
     * @param $photo
     * @param $path
     * @return mixed
     */
    public function generateThumbnails( $photo, $path = false)
    {

        // return if we have the thumbnails
        if ( $photo->has_thumbnails )
        {
            return $photo;
        }

        $thumbnail_sizes = null;

        // first we need to change if the photo is external
        if ( $photo->is_external )
        {
            // generate the new unique filename
            $filename = md5( $photo->file_name ) . time();
            $image = Image::make( $photo->sources['original'])->orientate()->encode('jpg');
            $path = ( !$path ) ? $this->generatePath() : $path;
            // put into the storage
            Storage::disk('s3')->put(  $path . '/' . $filename . '.jpg', $image->__toString(), 'public');
            // update db
            $photo->file_name = $filename;
            $photo->file_extension = 'jpg';
            $photo->file_type = 'image/jpeg';
            $photo->path = $path;
            $photo->is_external = false;
            $photo->external_url = null;
            $photo->save();

        } else {
            // take the default path
            $path = $photo->path;
            $filename = $photo->file_name;
        }

        foreach ( Config::get('photos.thumbnail_sizes') as $key => $size )
        {
            // Create the image, orientate and encode it as jpg ALWAYS
            $image = Image::make( $photo->sources['original'])->orientate()->encode('jpg');

            // Get the method
            $method = $size['method'];

            // If keep ratio
            if ( $size['keepRatio'] )
            {
                // add callback functionality to retain maximal original image size
                $image->$method( $size['width'], $size['height'], function( $constraint ) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                } );
            } else {
                // Just do the method
                $image->$method( $size['width'], $size['height'] );
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