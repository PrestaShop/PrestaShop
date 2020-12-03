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

use Employee;
use Exception;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Hook\HookDescription;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

/**
 * This command is used for appending the hook names in the configuration file.
 */
class AppendConfigurationFileHooksListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:update:configuration-file-hooks-listing')
            ->setDescription('Appends configuration file hooks list')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $this->initContext();

        $io = new SymfonyStyle($input, $output);

        if (!in_array($container->getParameter('kernel.environment'), ['dev', 'test'])) {
            $io->warning('Dev or test environment is required to fully list all the hooks');

            return;
        }

        $hookNames = $this->getHookNames();
        $hookDescriptions = $this->getHookDescriptions($hookNames);

        try {
            $addedHooks = $this->appendHooksInConfigurationFile($hookDescriptions);
        } catch (Exception $e) {
            $io->error($e->getMessage());
        }

        if (!empty($addedHooks)) {
            $io->title('Hooks added to configuration file');
            $io->note(sprintf('Total hooks added: %s', count($addedHooks)));

            return;
        }

        $io->note('No new hooks have been added to configuration file');
    }

    /**
     * Initialize PrestaShop Context
     */
    private function initContext()
    {
        /** @var LegacyContext $legacyContext */
        $legacyContext = $this->getContainer()->get('prestashop.adapter.legacy.context');
        //We need to have an employee or the listing hooks don't work
        //see LegacyHookSubscriber
        if (!$legacyContext->getContext()->employee) {
            //Even a non existing employee is fine
            $legacyContext->getContext()->employee = new Employee();
        }
    }

    /**
     * Gets all hooks names which need to be appended.
     *
     * @return string[]
     */
    private function getHookNames()
    {
        $container = $this->getContainer();

        $gridServiceIds = $container->getParameter('prestashop.core.grid.definition.service_ids');
        $optionsFormHookNames = $container->getParameter('prestashop.hook.option_form_hook_names');
        $identifiableObjectFormTypes = $container->getParameter('prestashop.core.form.identifiable_object.form_types');

        $gridDefinitionHooksProvider = $container->get(
            'prestashop.core.hook.provider.grid_definition_hook_by_service_ids_provider'
        );

        $identifiableObjectFormTypeProvider = $container->get(
            'prestashop.core.hook.provider.identifiable_object_hook_by_form_type_provider'
        );

        $gridDefinitionHookNames = $gridDefinitionHooksProvider->getHookNames($gridServiceIds);

        $identifiableObjectHookNames = $identifiableObjectFormTypeProvider->getHookNames($identifiableObjectFormTypes);

        return array_merge(
            $identifiableObjectHookNames,
            $optionsFormHookNames,
            $gridDefinitionHookNames
        );
    }

    /**
     * Appends given hooks in the configuration file.
     *
     * @param HookDescription[] $hookDescriptions
     *
     * @return array
     *
     * @throws Exception
     */
    private function appendHooksInConfigurationFile(array $hookDescriptions)
    {
        $hookConfigurationFileLocation = $this->getContainer()->get('kernel')->getRootDir() . '/../install-dev/data/xml/';
        $hookFileName = 'hook.xml';
        $fullFilePath = $hookConfigurationFileLocation . $hookFileName;

        $filesFinder = new Finder();
        $filesFinder
            ->files()
            ->in($hookConfigurationFileLocation)
            ->name($hookFileName)
        ;

        $hookFileContent = null;

        foreach ($filesFinder as $fileInfo) {
            $hookFileContent = $fileInfo->getContents();

            break;
        }

        if (!$hookFileContent) {
            throw new Exception(sprintf('File %s has not been found', $fullFilePath));
        }

        $xmlFileContent = new SimpleXMLElement($hookFileContent);

        if (!isset($xmlFileContent->entities, $xmlFileContent->entities->hook)) {
            return [];
        }

        $existingHookNames = $this->filterExistingHookNames($xmlFileContent->entities->hook);

        $addedHooks = [];
        foreach ($hookDescriptions as $hookDescription) {
            if (in_array($hookDescription->getName(), $existingHookNames)) {
                continue;
            }

            $hook = $xmlFileContent->entities->addChild('hook');

            $hook->addAttribute('id', $hookDescription->getName());
            $hook->addChild('name', $hookDescription->getName());
            $hook->addChild('title', $hookDescription->getTitle());
            $hook->addChild('description', $hookDescription->getDescription());

            $addedHooks[] = $hookDescription;
        }

        if (!$xmlFileContent->saveXML($fullFilePath)) {
            throw new Exception(sprintf('Failed to save new xml content to file %s', $fullFilePath));
        }

        return $addedHooks;
    }

    /**
     * Gets existing hook names which are already defined in the file.
     *
     * @param SimpleXMLElement $hooksFromXmlFile
     *
     * @return array
     */
    private function filterExistingHookNames(SimpleXMLElement $hooksFromXmlFile)
    {
        $hookNames = [];
        foreach ($hooksFromXmlFile as $hook) {
            if (!isset($hook->name)) {
                continue;
            }

            $hookNames[] = $hook->name->__toString();
        }

        return $hookNames;
    }

    /**
     * Gets hook descriptions
     *
     * @param array $hookNames
     *
     * @return HookDescription[]
     */
    private function getHookDescriptions(array $hookNames)
    {
        $descriptionGenerator = $this->getContainer()->get('prestashop.core.hook.generator.hook_description_generator');

        $descriptions = [];
        foreach ($hookNames as $hookName) {
            $hookDescription = $descriptionGenerator->generate($hookName);

            $descriptions[] = $hookDescription;
        }

        return $descriptions;
    }
}
