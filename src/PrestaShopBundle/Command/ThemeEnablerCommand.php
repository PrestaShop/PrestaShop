<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ThemeEnablerCommand extends Command
{
    /**
     * @var bool using CLI, the user must be allowed to enable themes
     */
    public const USER_ALLOWED_TO_ENABLE = true;

    /**
     * @var int if the activation of the theme fails, return the right code
     */
    public const RETURN_CODE_FAILED = 1;

    /**
     * @var ThemeManager
     */
    private $themeManager;

    public function __construct(ThemeManager $themeManager)
    {
        parent::__construct();
        $this->themeManager = $themeManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:theme:enable')
            ->setDescription('Manage your themes via command line')
            ->addArgument('theme', InputArgument::REQUIRED, 'Theme on which the action will be executed')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $theme = $input->getArgument('theme');

        $activationSuccess = $this->themeManager
            ->enable(
                $theme,
                self::USER_ALLOWED_TO_ENABLE
            )
        ;

        if (false === $activationSuccess) {
            $io->error(sprintf('The selected theme "%s" is invalid', $theme));

            return self::RETURN_CODE_FAILED;
        }

        $io->success(sprintf('Theme "%s" enabled with success.', $theme));

        return 0;
    }
}
