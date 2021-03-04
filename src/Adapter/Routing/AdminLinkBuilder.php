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

use Link;
use PrestaShop\PrestaShop\Core\Routing\EntityLinkBuilderInterface;

/**
 * Class AdminLinkBuilder is able to build entity links based on the Link::getAdminLink
 * method (which indirectly allows it to build symfony url as well).
 */
class AdminLinkBuilder implements EntityLinkBuilderInterface
{
    /** @var Link */
    private $link;

    /** @var array */
    private $entityControllers;

    /**
     * This class can manage entities based on the $entityControllers parameter,
     * you need to specify an array map with then entity/table short name and its
     * associated legacy controller:
     * e.g. $entityControllers = [
     *  'product' => 'AdminProducts',
     *  'customer' => 'AdminCustomers',
     * ];
     *
     * @param Link $link Link class that generates links
     * @param array $entityControllers List of entities with appropriate controller
     */
    public function __construct(Link $link, array $entityControllers)
    {
        $this->link = $link;
        $this->entityControllers = $entityControllers;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewLink($entity, array $parameters)
    {
        $controller = $this->entityControllers[$entity];
        $parameters = $this->buildActionParameters('view', $entity, $parameters);

        return $this->link->getAdminLink($controller, true, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getEditLink($entity, array $parameters)
    {
        $controller = $this->entityControllers[$entity];
        $parameters = $this->buildActionParameters('update', $entity, $parameters);

        return $this->link->getAdminLink($controller, true, $parameters);
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
        unset($parameters['token']);
        $editAction = $action . $entity;

        return array_merge(
            $parameters,
            [$editAction => 1]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function canBuild($entity)
    {
        return !empty($this->entityControllers[$entity]);
    }
}
