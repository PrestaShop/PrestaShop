<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeExporter;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use Smarty_Autoloader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

Smarty_Autoloader::register();

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

    protected function execute(InputInterface $input, OutputInterface $output): int
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
