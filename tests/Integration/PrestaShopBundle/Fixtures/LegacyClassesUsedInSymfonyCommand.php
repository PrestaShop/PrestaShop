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

namespace Tests\Integration\PrestaShopBundle\Fixtures;

use Shop;
use PrestaShop\PrestaShop\Core\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command display Shop status.
 */
class LegacyClassesUsedInSymfonyCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:test:shop-status')
            ->setDescription('Display shop(s) status(es)')
            ->setHelp('Use the "--all" option to display all shops information')
            ->addOption('all', '-a', InputOption::VALUE_NONE, 'Allows to display all shops information')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('all')) {
            $io->title('Shops statuses report');
            $shops = $this->getContainer()->get('prestashop.core.admin.shop.repository')->findAll();
            $io->table(
                ['ID', 'Name', 'Theme', 'Activated?', 'Deleted?'],
                $this->formatShopsInformation($shops)
            );

            return 0;
        }

        $shop = new Shop($input->getOption('id_shop'));

        if (null !== $shop->id) {
            $io->title(sprintf('Information for shop "%s"', $shop->name));

            $io->table(
                ['ID', 'Name', 'Theme', 'Activated?', 'Deleted?'],
                [
                    [$shop->id, $shop->name, $shop->theme_name, $shop->active ? '✔' : '✘', $shop->deleted ? '✔' : '✘'],
                ]
            );

            return 0;
        }

        $io->error(sprintf('Information for Shop with the id "%s" not found: did you set a valid "id_shop" option?', $shop->id));
    }

    /**
     * @param array $shops the list of the shops
     *
     * @return array
     */
    private function formatShopsInformation(array $shops)
    {
        $shopsInformation = [];
        /** @var Shop $shop */
        foreach ($shops as $shop) {
            $shopsInformation[] = [
                $shop->getId(),
                $shop->getName(),
                $shop->getThemeName(),
                $shop->getActive() ? '✔' : '✘',
                $shop->getDeleted() ? '✔' : '✘',
            ];
        }

        return $shopsInformation;
    }
}
