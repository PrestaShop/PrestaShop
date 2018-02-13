<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Command;

\Smarty_Autoloader::register();

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ExportThemeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('prestashop:theme:export')
            ->setDescription('Create zip to distribute theme with its dependencies')
            ->addArgument('theme', InputArgument::REQUIRED, 'Theme to export directory name.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('prestashop.core.addon.theme.repository');
        $theme = $repository->getInstanceByName($input->getArgument('theme'));

        $themeExporter = $this->getContainer()->get('prestashop.core.addon.theme.exporter');
        $path = $themeExporter->export($theme);

        $formatter = $this->getHelper('formatter');
        $translator = $this->getContainer()->get('translator');
        $successMsg = $translator->trans(
            'Your theme has been correctly exported: %path%',
            ['%path%' => $path],
            'Admin.Design.Notification'
        );
        $formattedBlock = $formatter->formatBlock($successMsg, 'info', true);
        $output->writeln($formattedBlock);
    }
}
