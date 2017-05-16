<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleCommand extends ContainerAwareCommand
{
    private $allowedActions = array(
        'install',
        'uninstall',
        'enable',
        'disable',
        'enable_mobile',
        'disable_mobile',
        'reset',
        'upgrade',
    );

    protected function configure()
    {
        $this
            ->setName('prestashop:module')
            ->setDescription('Manage your modules via command line')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)))
            ->addArgument('module name', InputArgument::REQUIRED, 'Module on which the action will be executed');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require($this->getContainer()->get('kernel')->getRootDir().'/../config/config.inc.php');

        $moduleName = $input->getArgument('module name');
        $action = $input->getArgument('action');

        $formatter = $this->getHelper('formatter');
        $translator = $this->getContainer()->get('translator');

        if (!in_array($action, $this->allowedActions)) {
            $msg = $translator->trans('Unknown module action. It must be one of these values: %actions%', array('%actions%' => implode(' / ', $this->allowedActions)), 'Admin.Modules.Notification');
            $formattedBlock = $formatter->formatBlock($msg, 'error', true);
            $output->writeln($formattedBlock);
            return;
        }

        /**
         * @var \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
         */
        $moduleManager = $this->getContainer()->get('prestashop.module.manager');
        if ($moduleManager->{$action}($moduleName)) {
            $msg = $translator->trans('%action% action on module %module% succeeded.',
                        array(
                            '%action%' => ucfirst(str_replace('_', ' ', $action)),
                            '%module%' => $moduleName, ),
                        'Admin.Notifications.Success');
            $formattedBlock = $formatter->formatBlock($msg, 'info', true);
            $output->writeln($formattedBlock);
            return;
        }

        $error = $moduleManager->getError($moduleName);
        $msg = $translator->trans(
            'Cannot %action% module %module%. %error_details%',
            array(
                '%action%' => str_replace('_', ' ', $action),
                '%module%' => $moduleName,
                '%error_details%' => $error, ),
            'Admin.Notifications.Error'
        );
        $formattedBlock = $formatter->formatBlock($msg, 'error', true);
        $output->writeln($formattedBlock);
    }
}