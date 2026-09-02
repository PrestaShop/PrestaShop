<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener\Console;

use PrestaShop\PrestaShop\Adapter\Shop\Context;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputOption;

/**
 * Adds to optional input options to all the console commands:
 *  - id_shop to specify a shop context
 *  - id_shop_group to specify a shop group context
 */
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
