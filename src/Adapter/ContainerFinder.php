<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter;

use Context;
use Controller;
use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Find the container
 */
class ContainerFinder
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * ContainerFinder constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return ContainerBuilder|ContainerInterface|null
     *
     * @throws Exception
     */
    public function getContainer()
    {
        if (isset($this->context->container)) {
            return $this->context->container;
        }
        if (isset($this->context->controller)
            && $this->context->controller instanceof Controller
            && ($container = $this->context->controller->getContainer())
            && null !== $container
        ) {
            return $container;
        }
        $container = SymfonyContainer::getInstance();
        if (null !== $container) {
            return $container;
        }

        throw new Exception('Kernel Container is not available');
    }
}
