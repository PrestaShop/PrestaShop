<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Image;

use Db;

class ImageTypeRepository
{
    /**
     * @var Db
     */
    private $db;
    /**
     * @var string
     */
    private $db_prefix;

    public function __construct(Db $db)
    {
        $this->db = $db;
        $this->db_prefix = $db->getPrefix();
    }

    /**
     * Applies the image types declared by a theme. Types are upserted by name: a theme only
     * creates or updates its own types and leaves every other row untouched. The table used to be
     * truncated first, which silently wiped image types the merchant had created by hand as well as
     * the types required by the theme of another shop (image_type is a global, non shop-scoped
     * table). Truncating is therefore avoided.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/20300
     */
    public function setTypes(array $types)
    {
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

        // image_type.name is unique: update the existing type instead of failing on a duplicate.
        $existingId = $this->getIdByName($name);
        if ($existingId) {
            $this->db->update('image_type', $data, 'id_image_type = ' . $existingId);

            return $existingId;
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

        return (int) $id_image_type;
    }
}
