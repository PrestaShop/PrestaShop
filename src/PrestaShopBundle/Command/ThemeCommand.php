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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManager;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeExporter;
use Context;
use Employee;
use Shop;
use Validate;

/**
 * Class ThemeCommand run command actions in CLI
 */
class ThemeCommand extends ContainerAwareCommand
{
    const ALLOWED_ACTIONS = array(
        'install',
        'enable',
        'export',
        'uninstall',
    );

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var int if the activation of the theme fails, return the right code
     */
    const RETURN_CODE_FAILED = 1;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:theme')
            ->setDescription('Manage your theme via command line')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', self::ALLOWED_ACTIONS)))
            ->addArgument('theme', InputArgument::REQUIRED, 'Theme or path to zip on which the action will be executed')
            ->addArgument('shop', InputArgument::OPTIONAL, 'Shop id on which the action will be executed');
    }

    /**
     * {@inheritdoc}
     */
    protected function init(InputInterface $input, OutputInterface $output)
    {
        // the user must be allowed to enable themes
        Context::getContext()->employee = new Employee(1);

        $shopId = $input->getArgument('shop');
        if ($shopId !== null) {
            if (Validate::isLoadedObject($shop = new Shop($shopId))) {
                Context::getContext()->shop = $shop;
            } else {
                $this->io->error(sprintf('The selected shop id "%s" is invalid', $shop));

                return self::RETURN_CODE_FAILED;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $action = $input->getArgument('action');
        $theme = $input->getArgument('theme');
        $this->init($input, $output);

        if (!in_array($action, self::ALLOWED_ACTIONS)) {
            $this->io->error(sprintf('Unknown theme action. It must be one of these values: "%s"', implode(' / ', self::ALLOWED_ACTIONS)));

            return self::RETURN_CODE_FAILED;
        }

        switch ($action) {
            case 'export':
                $this->executeExportThemeAction($theme);
                break;
            case 'install':
                if (!filter_var($theme, FILTER_VALIDATE_URL)
                    && !file_exists($theme)) {
                    $this->io->error('Zip file doesn\'t exist or url is not valid');

                    return self::RETURN_CODE_FAILED;
                }
            default:
                $this->executeGenericThemeAction($action, $theme);
                break;
        }
    }

    protected function executeExportThemeAction($themeName)
    {
        /**
         * @var ThemeRepository
         */
        $theme = $this->getContainer()
            ->get('prestashop.core.addon.theme.repository')
            ->getInstanceByName(
                $themeName
            )
        ;
        /**
         * @var ThemeExporter
         */
        $path = $this->getContainer()
            ->get('prestashop.core.addon.theme.exporter')
            ->export($theme)
        ;

        if (false === $path) {
            $this->io->error(sprintf('Error occured during theme export'));

            return self::RETURN_CODE_FAILED;
        }
        $this->io->success(sprintf('Your theme has been correctly exported: "%s".', $path));
    }

    protected function executeGenericThemeAction($action, $argument)
    {
        /**
         * @var ThemeManager
         */
        $themeActionSuccess = $this->getContainer()
            ->get('prestashop.core.addon.theme.theme_manager')
            ->{$action}($argument)
        ;
        if (false === $themeActionSuccess) {
            $this->io->error(sprintf('%1$s action on theme failed.', ucfirst($action)));

            return self::RETURN_CODE_FAILED;
        }
        $this->io->success(sprintf('%1$s action on theme succeeded.', ucfirst($action)));
    }
}
