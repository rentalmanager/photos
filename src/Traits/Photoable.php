<?php
namespace RentalManager\Photos\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use RentalManager\RelationHelper\Facades\RelationHelper;
use InvalidArgumentException;

/**
 * Created by PhpStorm.
 * Date: 7/4/18
 * Time: 12:23 PM
 * Photoable.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */

trait Photoable
{


    /**
     * Attach a photo
     *
     * @param $photo
     * @param int $ordering
     * @return $this
     */
    public function attachPhoto($photo, $ordering = 0)
    {
        return $this->attachPhotoableModel('photos', $photo, ['ordering' => $ordering]);
    }

    /**
     * Detach a photo
     *
     * @param $photo
     * @return static
     */
    public function detachPhoto( $photo )
    {
        return $this->detachPhotoableModel('photos', $photo);
    }


    /**
     * Detach photos
     *
     * @param array $photos
     * @return $this
     */
    public function detachPhotos($photos = [])
    {
        if ( empty( $photos ) )
        {
            $photos = $this->photos()->get();
        }

        foreach ( $photos as $photo )
        {
            $this->detachPhoto($photo);
        }
        return $this;
    }


    /**
     * Sync photos and apply automatic ordering by id
     * You will need to provide an array of image IDS by order like this:
     *
     * $photos = [1, 4, 78, 89, 2];
     *
     * If you are applying the empty array, the photos will be detached
     *
     * @param array $photos
     * @return mixed
     */
    public function syncPhotos($photos = [])
    {
        if ( empty( $photos ) )
        {
            return $this->detachPhotos();
        }

        $do = [];
        $i = 0;
        foreach ( $photos as $photo )
        {
            $do[$photo] = ['ordering' => $i];
            $i++;
        }
        return $this->photos()->sync($do, true);
    }


    // GETTERS
    // ---------------------------

    /**
     * Get the photos ordered by ordering asc
     *
     * @return mixed
     */
    public function getOrderedPhotos()
    {
        return $this->photos()->orderBy( Config::get('photos.tables.photo_nodes') . '.ordering', 'asc')->get();
    }

    // RELATIONS
    // ---------------------------

    /**
     * Get the list of all photos
     *
     * @return mixed
     */
    public function photos()
    {
        return $this->morphToMany(
            Config::get('photos.models.photo'), // model
            'node', // node
            Config::get('photos.tables.photo_nodes'), // table
            'node_id',
            Config::get('photos.foreign_keys.photo')
        );
    }

    // ALIASES
    // ---------------------------

    /**
     * Alias to eloquent attach() method
     *
     * @param $relationship
     * @param $object
     * @param $attributes
     * @return $this
     */
    private function attachPhotoableModel($relationship, $object, $attributes = [])
    {
        if ( !RelationHelper::isValidRelationship($relationship) )
        {
            throw new InvalidArgumentException;
        }

        $objectType = Str::singular($relationship);
        $object = RelationHelper::getIdFor($object, $objectType, 'photos');

        $this->$relationship()->attach(
            $object,
            $attributes
        );

        return $this;
    }

    /**
     * Alias to eloquent many-to-many relation's detach() method
     *
     * @param string $relationship
     * @param mixed $object
     * @return static
     */
    private function detachPhotoableModel($relationship, $object)
    {
        if ( !RelationHelper::isValidRelationship($relationship) )
        {
            throw new InvalidArgumentException;
        }

        $objectType = Str::singular($relationship);
        $relationshipQuery = $this->$relationship();

        $object = RelationHelper::getIdFor($object, $objectType, 'photos');

        $relationshipQuery->detach($object);

        return $this;
    }
}