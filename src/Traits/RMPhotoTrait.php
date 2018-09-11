<?php
namespace RentalManager\Photos\Traits;

use Illuminate\Support\Facades\Config;

/**
 * Created by PhpStorm.
 * Date: 7/4/18
 * Time: 12:23 PM
 * PhotosPhotoTrait.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */

trait RMPhotoTrait
{

    /**
     * Get the full path to the image
     *
     * @return string
     */
    protected function fullPath()
    {

        if ( $this->is_external )
        {
            if ( $this->path )
            {
                $ph = '/' . $this->path;
            } else {
                $ph = null;
            }

            return $this->external_url . $ph;
        } else {

            // switch the disk
            switch ( $this->disk )
            {
                case 'local':

                    return Config::get('app.url') . '/' . $this->path;
                    break;

                case 's3':
                    return Config::get('photos.storage_path') . '/' . $this->path;
                    break;
            }
        }
    }


    /**
     * Generate the sources attribute
     * @return array
     */
    public function getSourcesAttribute()
    {
        $sources = [];

        $fullPath  = $this->fullPath();

        if ( $this->file_extension != null || $this->file_extension != '' )
        {
            $sources['original'] = $fullPath . '/' . $this->file_name . '.' . $this->file_extension;
        } else {
            $sources['original'] = $fullPath . '/' . $this->file_name;
        }

        if ( $this->has_thumbnails )
        {

            foreach ( $this->thumbnails as $key => $size )
            {
                $sources[$key] = $fullPath . '/' . $size . '.' . $this->file_extension;
            }
        }

        return $sources;
    }

    /**
     * Set thumbnails attribute as the json encoded string
     *
     * @param $value
     */
    public function setThumbnailsAttribute($value)
    {
        $this->attributes['thumbnails'] = ( $value ) ? json_encode($value) : null;
    }

    /**
     * Return thumbnails attribute as the json decoded array
     *
     * @param $value
     * @return mixed|null
     */
    public function getThumbnailsAttribute($value)
    {
        return ( $value ) ? json_decode( $value, true ) : null;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (!preg_match('/^can[A-Z].*/', $method)) {
            return parent::__call($method, $parameters);
        }
    }
}
