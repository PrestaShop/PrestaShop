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
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Hook\HookDescription;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Appends sql upgrade file with the sql which can be used to create new hooks.
 */
class AppendHooksListForSqlUpgradeFileCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:update:sql-upgrade-file-hooks-listing')
            ->setDescription(
                'Adds sql to sql upgrade file which contains hook insert opeartion'
            )
            ->addArgument(
                'ps-version',
                InputArgument::REQUIRED,
                'The prestashop version for which sql upgrade file will be searched'
            )
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
        $hookNames = $this->getWithoutRegisteredHooks($hookNames);

        if (empty($hookNames)) {
            $io->note('No hooks found.');

            return;
        }

        $hookDescriptions = $this->getHookDescriptions($hookNames);

        try {
            $sqlUpgradeFile = $this->getSqlUpgradeFileByPrestaShopVersion($input->getArgument('ps-version'));
        } catch (FileNotFoundException $exception) {
            $io->error($exception->getMessage());

            return;
        }

        $sqlInsertStatement = $this->getSqlInsertStatement($hookDescriptions);

        $this->appendSqlToFile($sqlUpgradeFile->getFileInfo()->getPathName(), $sqlInsertStatement);

        $io->success(
            sprintf(
                'All %s hooks have been listed to file %s',
                count($hookNames),
                $sqlUpgradeFile->getFileInfo()->getPathName()
            )
        );
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
     * Gets sql upgrade file by PrestaShop version.
     *
     * @param string $version
     *
     * @return SplFileInfo
     */
    private function getSqlUpgradeFileByPrestaShopVersion($version)
    {
        $sqlUpgradeFilesLocation = $this->getContainer()->get('kernel')->getRootDir() . '/../install-dev/upgrade/sql/';
        $sqlUpgradeFile = $version . '.sql';

        $filesFinder = new Finder();
        $filesFinder
            ->files()
            ->in($sqlUpgradeFilesLocation)
            ->name($sqlUpgradeFile)
        ;

        $filesCount = $filesFinder->count();

        if (1 !== $filesCount) {
            throw new FileNotFoundException(sprintf('Expected to find 1 file but %s files found with name %s', $filesFinder->count(), $sqlUpgradeFile));
        }

        foreach ($filesFinder as $sqlInfo) {
            return $sqlInfo;
        }

        return null;
    }

    /**
     * Gets sql insert statement.
     *
     * @param HookDescription[] $hookDescriptions
     *
     * @return string
     */
    private function getSqlInsertStatement(array $hookDescriptions)
    {
        $valuesToInsert = [];
        foreach ($hookDescriptions as $hookDescription) {
            $valuesToInsert[] = sprintf(
                '(NULL,"%s","%s","%s","1")',
                pSQL($hookDescription->getName()),
                pSQL($hookDescription->getTitle()),
                pSQL($hookDescription->getDescription())
            );
        }

        if (empty($valuesToInsert)) {
            return '';
        }

        return sprintf(
            'INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES %s;',
            implode(',', $valuesToInsert)
        );
    }

    /**
     * Appends new content to the given file.
     *
     * @param string $pathToFile
     * @param string $content
     */
    private function appendSqlToFile($pathToFile, $content)
    {
        $fileSystem = new FileSystem();

        $fileSystem->appendToFile($pathToFile, $content);
    }

    /**
     * Filters out already registered hooks.
     *
     * @param array $hookNames
     *
     * @return array
     */
    private function getWithoutRegisteredHooks(array $hookNames)
    {
        $hooksProvider = $this->getContainer()->get('prestashop.adapter.legacy.hook');
        $registeredHooks = $hooksProvider->getHooks();
        $registeredHookNames = array_column($registeredHooks, 'name');

        return array_diff($hookNames, $registeredHookNames);
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
