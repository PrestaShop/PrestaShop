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
            ->addArgument('theme', InputArgument::REQUIRED, 'Module on which the action will be executed')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
