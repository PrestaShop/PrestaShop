<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
