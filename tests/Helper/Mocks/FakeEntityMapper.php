<?php


namespace PrestaShop\PrestaShop\Tests\Helper\Mocks;

use Adapter_EntityMapper;
use Exception;
use ObjectModel;

class FakeEntityMapper extends Adapter_EntityMapper
{
    private $fake_db = array();

    private $entity_being_built = null;

    /**
     * Stores the given entity in the fake database, so load call with the same id will fill the entity with it.
     * @param ObjectModel $entity
     * @return $this
     * @throws Exception
     */
    public function willReturn(ObjectModel $entity)
    {
        if ($this->entity_being_built !== null) {
            throw new Exception('Invalid usage of FakeEntityMapper::willReturn : an entity build was already started, please call FakeEntityMapper::forId to finish building your entity.');
        }

        $this->entity_being_built = $entity;

        return $this;
    }

    /**
     * @param $id
     * @param null $id_lang
     * @param null $id_shop
     * @throws Exception
     */
    public function forId($id, $id_lang = null, $id_shop = null)
    {
        if ($this->entity_being_built === null) {
            throw new Exception('Invalid usage of FakeEntityMapper::forId : you need to call willReturn first.');
        }

        $cache_id = $this->buildCacheId($id, get_class($this->entity_being_built), $id_lang, $id_shop);
        $this->fake_db[$cache_id] = $this->entity_being_built;

        $this->entity_being_built = null;
    }

    /**
     * Fills the given entity with fields from the entity stored in the fake database if it exists.
     * @param $id
     * @param $id_lang
     * @param $entity
     * @param $entity_defs
     * @param $id_shop
     */
    public function load($id, $id_lang, $entity, $entity_defs, $id_shop, $should_cache_objects)
    {
        if ($this->entity_being_built !== null) {
            throw new Exception('Unifinished entity build : an entity build was started with FakeEntityMapper::willReturn, please call FakeEntityMapper::forId to finish building your entity.');
        }

        $cache_id = $this->buildCacheId($id, $entity_defs['classname'], $id_lang, $id_shop);

        if (isset($this->fake_db[$cache_id])) {
            foreach ($this->fake_db[$cache_id] as $key => $value) {
                $entity->$key = $value;
            }
        }
    }


    private function buildCacheId($id, $class_name, $id_lang, $id_shop)
    {
        return 'objectmodel_' . $class_name . '_' . (int)$id . '_' . (int)$id_shop . '_' . (int)$id_lang;
    }
}
