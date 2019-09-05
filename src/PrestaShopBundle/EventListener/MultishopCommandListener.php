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

namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Adapter\Shop\Context;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputOption;

class MultishopCommandListener
{
    public $context;

    /**
     * Path to root dir, needed to require config file.
     *
     * @var string
     */
    public $rootDir;

    public function __construct(Context $context, $rootDir)
    {
        $this->context = $context;
        $this->rootDir = $rootDir;
    }

    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $definition = $event->getCommand()->getDefinition();
        $input = $event->getInput();

        $definition->addOption(new InputOption('id_shop', null, InputOption::VALUE_OPTIONAL, 'Specify shop context.'));
        $definition->addOption(new InputOption('id_shop_group', null, InputOption::VALUE_OPTIONAL, 'Specify shop group context.'));
        $input->bind($definition);

        $id_shop = $input->getOption('id_shop');
        $id_shop_group = $input->getOption('id_shop_group');

        if ($id_shop && $id_shop_group) {
            throw new LogicException('Do not specify an ID shop and an ID group shop at the same time.');
        }

        if ($id_shop) {
            // Unfortunately, there is SQL requests executed in the legacy. I have to include the config file.
            $this->fixUnloadedConfig();
            $this->context->setShopContext($id_shop);
        }
        if ($id_shop_group) {
            $this->context->setShopGroupContext($id_shop_group);
        }
    }

    /**
     * This function is an hack.
     * Calling setShopContext will trigger a sql request, we need to be sure the config is properly loaded.
     */
    private function fixUnloadedConfig()
    {
        if (!defined('_DB_PREFIX_')) {
            require_once $this->rootDir . '/../config/config.inc.php';
        }
    }
}
