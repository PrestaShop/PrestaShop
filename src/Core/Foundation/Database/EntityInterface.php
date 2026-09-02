<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Database;

interface EntityInterface
{
    /**
     * Returns the name of the repository class for this entity.
     * If unspecified, a generic repository will be used for the entity.
     *
     * @return string or false value
     */
    public static function getRepositoryClassName();

    public function save();

    public function delete();

    public function hydrate(array $keyValueData);
}
