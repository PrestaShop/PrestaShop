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

namespace PrestaShop\PrestaShop\Core\Console;

use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\LogicException;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Adapter of Symfony Command class for PrestaShop
 *
 * @author Mickaël Andrieu <mickael.andrieu@prestashop.com>
 */
abstract class Command extends ContainerAwareCommand
{
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        require_once $container->get('kernel')->getRootDir() . '/../config/config.inc.php';

        /** @var LegacyContext $legacyContext */
        $legacyContext = $container->get('prestashop.adapter.legacy.context');

        /** @var ShopContext $shopContext */
        $shopContext = $container->get('prestashop.adapter.shop.context');

        // Set an employee
        $employeeId = $input->getOption('employee');
        $shopId = $input->getOption('shop');
        $shopGroupId = $input->getOption('shop_group');

        if ($shopId && $shopGroupId) {
            throw new LogicException('Do not specify an ID shop and an ID group shop at the same time.');
        }

        $legacyContext->getContext()->controller = new \stdClass();
        $legacyContext->getContext()->controller->controller_type = 'console';

        if (!$legacyContext->getContext()->employee) {
            $shopContext->setShopContext($shopId);
            $shopContext->setShopGroupContext($shopGroupId);
            $legacyContext->getContext()->employee = new Employee($employeeId);
        }

        return parent::initialize($input, $output);
    }
}
