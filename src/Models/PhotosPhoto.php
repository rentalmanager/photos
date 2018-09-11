<?php
namespace RentalManager\Photos\Models;

use Illuminate\Database\Eloquent\Model;
use RentalManager\Photos\Traits\RMPhotoTrait;


/**
 * Created by PhpStorm.
 * Date: 7/4/18
 * Time: 12:23 PM
 * PhotosPhoto.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */

class RMPhoto extends Model
{

    use RMPhotoTrait;

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
        $this->table = 'photos';
    }
}
