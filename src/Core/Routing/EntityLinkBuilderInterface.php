<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Routing;

/**
 * Interface EntityLinkBuilderInterface is able to build links for entities, like
 * edit or view link. Each interface is able to say if it can manage a certain type
 * of entity.
 */
interface EntityLinkBuilderInterface
{
    /**
     * @param string $entity
     * @param array $parameters
     *
     * @return string
     */
    public function getViewLink($entity, array $parameters);

    /**
     * @param string $entity
     * @param array $parameters
     *
     * @return string
     */
    public function getEditLink($entity, array $parameters);

    /**
     * @param string $entity
     *
     * @return bool
     */
    public function canBuild($entity);
}
