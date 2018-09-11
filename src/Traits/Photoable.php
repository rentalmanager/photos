<?php
namespace RentalManager\Photos\Traits;

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
        return $this->photos()->orderBy(  'photo_node.ordering', 'asc')->get();
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
            'App\RentalManager\AddOns\Photo', // model
            'node', // node
            'photo_node', // table
            'node_id',
            'photo_id'
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
        $relationshipQuery = $this->$relationship();
        $relationshipQuery->detach($object);

        return $this;
    }
}
