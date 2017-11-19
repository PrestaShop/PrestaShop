<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace Tests\TestCase;

use PrestaShop\PrestaShop\Adapter\EntityMapper;
use Exception;
use ObjectModel;

class FakeEntityMapper extends EntityMapper
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
