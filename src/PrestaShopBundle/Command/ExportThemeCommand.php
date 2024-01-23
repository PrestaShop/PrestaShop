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

namespace PrestaShopBundle\Command;

\Smarty_Autoloader::register();

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeExporter;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExportThemeCommand extends Command
{
    /**
     * @var ThemeRepository
     */
    private $themeRepository;

    /**
     * @var ThemeExporter
     */
    private $themeExporter;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ThemeRepository $themeRepository,
        ThemeExporter $themeExporter,
        TranslatorInterface $translator
    ) {
        parent::__construct();
        $this->themeRepository = $themeRepository;
        $this->themeExporter = $themeExporter;
        $this->translator = $translator;
    }

    protected function configure()
    {
        $this
            ->setName('prestashop:theme:export')
            ->setDescription('Create zip to distribute theme with its dependencies')
            ->addArgument('theme', InputArgument::REQUIRED, 'Theme to export directory name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $theme = $this->themeRepository->getInstanceByName($input->getArgument('theme'));

        $path = $this->themeExporter->export($theme);

        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');
        $successMsg = $this->translator->trans(
            'Your theme has been correctly exported: %path%',
            ['%path%' => $path],
            'Admin.Design.Notification'
        );
        $formattedBlock = $formatter->formatBlock($successMsg, 'info', true);
        $output->writeln($formattedBlock);

        return 0;
    }
}
