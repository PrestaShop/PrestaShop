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

namespace PrestaShopBundle\Command;

use PrestaShopBundle\Routing\Linter\LegacyLinksChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckLegacyLinksCommand extends Command
{
    /**
     * @var LegacyLinksChecker
     */
    private $legacyLinksChecker;

    /**
     * @param LegacyLinksChecker $legacyLinksChecker
     * @param string $name
     */
    public function __construct(LegacyLinksChecker $legacyLinksChecker, $name = null)
    {
        parent::__construct($name);

        $this->legacyLinksChecker = $legacyLinksChecker;
    }

    /**
     * Configures command
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:routes:check-legacy-links')
            ->setDescription('Returns routes where legacy links are missing');
    }

    /**
     * Executes command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $routes = $this->legacyLinksChecker->check();
        $count = count($routes);
        $output->writeln("Found $count routes missing legacy links.");
        $output->writeln($routes);
    }
}
