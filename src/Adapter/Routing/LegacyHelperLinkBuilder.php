<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Routing;

use PrestaShop\PrestaShop\Core\Routing\EntityLinkBuilderInterface;

/**
 * Class LegacyHelperLinkBuilder is able to build entity links "manually" by concatenating
 * the parameters to the current index. This way of building links is deprecated and should
 * be replaced with Symfony router or Link::getAdminLink
 */
class LegacyHelperLinkBuilder implements EntityLinkBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getViewLink($entity, array $parameters)
    {
        $currentIndex = $parameters['current_index'];
        $parameters = $this->buildActionParameters('view', $entity, $parameters);

        return $currentIndex . '&' . http_build_query($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getEditLink($entity, array $parameters)
    {
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
        $viewAction = $action . $entity;
        $entityId = 'id_' . $entity;
        $parameters = array_merge(
            $parameters,
            [$entityId => $parameters[$entityId], $viewAction => 1]
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
