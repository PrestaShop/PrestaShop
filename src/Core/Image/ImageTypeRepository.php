<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Core\Image;

use Db;
use Exception;
use Shop;

class ImageTypeRepository
{
    private $shop;
    private $db;
    private $db_prefix;

    public function __construct(
        Shop $shop,
        Db $db
    ) {
        $this->shop = $shop;
        $this->db = $db;
        $this->db_prefix = $db->getPrefix();
    }

    public function setTypes(array $types)
    {
        $this->removeAllTypes();
        foreach ($types as $name => $data) {
            $this->createType(
                $name,
                $data['width'],
                $data['height'],
                $data['scope']
            );
        }
        return $this;
    }

    public function createType($name, $width, $height, array $scope)
    {
        $data = [
            'name' => $this->db->escape($name),
            'width' => $this->db->escape($width),
            'height' => $this->db->escape($height),
        ];

        foreach ($this->getScopeList() as $scope_item) {
            if (in_array($scope_item, $scope)) {
                $data[$scope_item] = 1;
            } else {
                $data[$scope_item] = 0;
            }
        }

        $this->db->insert('image_type', $data);

        return $this->getIdByName($name);
    }

    public function getScopeList()
    {
        return ['products', 'categories', 'manufacturers', 'suppliers', 'stores'];
    }

    public function getIdByName($name)
    {
        $escaped_name = $this->db->escape($name);

        $id_image_type = $this->db->getValue(
            "SELECT id_image_type FROM {$this->db_prefix}image_type WHERE name = '$escaped_name'"
        );

        return (int)$id_image_type;
    }

    protected function removeAllTypes()
    {
        Db::getInstance()->execute(
            "TRUNCATE TABLE {$this->db_prefix}image_type"
        );
    }
}
