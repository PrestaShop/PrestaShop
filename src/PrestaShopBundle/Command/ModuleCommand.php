<?php
/**
 * 2007-2018 PrestaShop.
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
        'configure',
    );

    /**
     * @var \Symfony\Component\Console\Helper\FormatterHelper
     */
    protected $formatter;

    /**
     * @var \PrestaShopBundle\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Symfony\Component\Console\Input\Input
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\Output
     */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('prestashop:module')
            ->setDescription('Manage your modules via command line')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)))
            ->addArgument('module name', InputArgument::REQUIRED, 'Module on which the action will be executed')
            ->addArgument('file path', InputArgument::OPTIONAL, 'YML file path for configuration');
    }

    protected function init(InputInterface $input, OutputInterface $output)
    {
        $this->formatter = $this->getHelper('formatter');
        $this->translator = $this->getContainer()->get('translator');
        $this->input = $input;
        $this->output = $output;
        require $this->getContainer()->get('kernel')->getRootDir().'/../config/config.inc.php';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input, $output);

        $moduleName = $input->getArgument('module name');
        $action = $input->getArgument('action');
        $file = $input->getArgument('file path');

        if (!in_array($action, $this->allowedActions)) {
            $this->displayMessage(
                $this->translator->trans(
                    'Unknown module action. It must be one of these values: %actions%',
                    array('%actions%' => implode(' / ', $this->allowedActions)),
                    'Admin.Modules.Notification'),
                'error');

            return;
        }

        if ('configure' === $action) {
            $this->executeConfigureModuleAction($moduleName, $file);
        } else {
            $this->executeGenericModuleAction($action, $moduleName);
        }
    }

    protected function executeConfigureModuleAction($moduleName, $file = null)
    {
        $moduleSelfConfigurator = $this->getContainer()->get('prestashop.adapter.module.self_configurator');
        $moduleSelfConfigurator->module($moduleName);
        if ($file) {
            $moduleSelfConfigurator->file($file);
        }

        // Check if validation passed and exit in case of errors
        $errors = $moduleSelfConfigurator->validate();
        if (!empty($errors)) {
            // Display errors as a list
            $errors = array_map(function ($val) { return '- '.$val; }, $errors);
            // And add a default message at the top
            array_unshift($errors, $this->translator->trans(
                'Validation of configuration details failed:',
                array(),
                'Admin.Modules.Notification'
            ));
            $this->displayMessage($errors, 'error');

            return;
        }

        // Actual configuration
        $moduleSelfConfigurator->configure();
        $this->displayMessage(
            $this->translator->trans('Configuration successfully applied.', array(), 'Admin.Modules.Notification'),
            'info');
    }

    protected function executeGenericModuleAction($action, $moduleName)
    {
        /**
         * @var \PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager
         */
        $moduleManager = $this->getContainer()->get('prestashop.module.manager');
        if ($moduleManager->{$action}($moduleName)) {
            $this->displayMessage(
                $this->translator->trans(
                    '%action% action on module %module% succeeded.',
                    array(
                        '%action%' => ucfirst(str_replace('_', ' ', $action)),
                        '%module%' => $moduleName, ),
                    'Admin.Modules.Notification')
            );

            return;
        }

        $error = $moduleManager->getError($moduleName);
        $this->displayMessage(
            $this->translator->trans(
                'Cannot %action% module %module%. %error_details%',
                array(
                    '%action%' => str_replace('_', ' ', $action),
                    '%module%' => $moduleName,
                    '%error_details%' => $error, ),
                'Admin.Modules.Notification'
            ), 'error'
        );
    }

    protected function displayMessage($message, $type = 'info')
    {
        $this->output->writeln(
            $this->formatter->formatBlock($message, $type, true)
        );
    }
}
