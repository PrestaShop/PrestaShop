<?php

interface Core_Foundation_Database_Entity
{
    /**
     * Returns the name of the repository class for this entity.
     * If unspecified, a generic repository will be used for the entity.
     *
     * @return string or falsey value
     */
    public static function getRepositoryClassName();

    public function save();
}
