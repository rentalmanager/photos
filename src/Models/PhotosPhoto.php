<?php
namespace RentalManager\Photos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use RentalManager\Photos\Traits\PhotosPhotoTrait;


/**
 * Created by PhpStorm.
 * Date: 7/4/18
 * Time: 12:23 PM
 * PhotosPhoto.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */

class PhotosPhoto extends Model
{

    use PhotosPhotoTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;


    /**
     * Model constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Config::get('photos.tables.photos');
    }
}