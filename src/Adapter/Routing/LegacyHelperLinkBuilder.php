<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Routing;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Routing\EntityLinkBuilderInterface;

/**
 * Class LegacyHelperLinkBuilder is able to build entity links "manually" by concatenating
 * the parameters to the current index. This way of building links is deprecated and should
 * be replaced with Symfony router or Link::getAdminLink
 */
class LegacyHelperLinkBuilder implements EntityLinkBuilderInterface
{
    /**
     * @param string $entity
     * @param array $parameters
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function getViewLink($entity, array $parameters)
    {
        if (!isset($parameters['current_index'])) {
            throw new InvalidArgumentException('Missing parameter current_index to build legacy link');
        }

        $currentIndex = $parameters['current_index'];
        $parameters = $this->buildActionParameters('view', $entity, $parameters);

        return $currentIndex . '&' . http_build_query($parameters);
    }

    /**
     * @param string $entity
     * @param array $parameters
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function getEditLink($entity, array $parameters)
    {
        if (!isset($parameters['current_index'])) {
            throw new InvalidArgumentException('Missing parameter current_index to build legacy link');
        }

        $currentIndex = $parameters['current_index'];
        $parameters = $this->buildActionParameters('update', $entity, $parameters);

        return $currentIndex . '&' . http_build_query($parameters);
    }

    /**
     * @param string $action
     * @param string $entity
     * @param array $parameters
     *
     * @return array
     */
    private function buildActionParameters($action, $entity, array $parameters)
    {
        unset($parameters['current_index']);
        $actionParameter = $action . $entity;

        /**
         * Legacy actions are displayed with empty value (e.g ?controller=ProductAdminController&updateproduct&id_product=1)
         * Some modules don't just check that the parameter is set but also that it is empty...
         * The closest thing we have with http_build_query is controller=ProductAdminController&updateproduct=&id_product=1
         */
        $parameters = array_merge(
            [$actionParameter => ''],
            $parameters
        );

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function canBuild($entity)
    {
        return true;
    }
}
