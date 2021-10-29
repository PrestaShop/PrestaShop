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
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Hook\Generator\HookDescriptionGenerator;
use PrestaShop\PrestaShop\Core\Hook\HookDescription;
use PrestaShop\PrestaShop\Core\Hook\Provider\GridDefinitionHookByServiceIdsProvider;
use PrestaShop\PrestaShop\Core\Hook\Provider\IdentifiableObjectHookByFormTypeProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Appends sql upgrade file with the sql which can be used to create new hooks.
 */
class AppendHooksListForSqlUpgradeFileCommand extends Command
{
    /**
     * @var string
     */
    private $env;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var GridDefinitionHookByServiceIdsProvider
     */
    private $gridDefinitionHookByServiceIdsProvider;

    /**
     * @var IdentifiableObjectHookByFormTypeProvider
     */
    private $identifiableObjectHookByFormTypeProvider;

    /**
     * @var HookInformationProvider
     */
    private $hookInformationProvider;

    /**
     * @var HookDescriptionGenerator
     */
    private $hookDescriptionGenerator;

    /**
     * @var array
     */
    private $serviceIds;

    /**
     * @var array
     */
    private $optionFormHookNames;

    /**
     * @var array
     */
    private $formTypes;

    /**
     * @var string
     */
    private $sqlUpgradePath;

    public function __construct(
        string $env,
        LegacyContext $legacyContext,
        GridDefinitionHookByServiceIdsProvider $gridDefinitionHookByServiceIdsProvider,
        IdentifiableObjectHookByFormTypeProvider $identifiableObjectHookByFormTypeProvider,
        HookInformationProvider $hookInformationProvider,
        HookDescriptionGenerator $hookDescriptionGenerator,
        array $serviceIds,
        array $optionFormHookNames,
        array $formTypes,
        string $sqlUpgradePath
    ) {
        parent::__construct();
        $this->env = $env;
        $this->legacyContext = $legacyContext;
        $this->gridDefinitionHookByServiceIdsProvider = $gridDefinitionHookByServiceIdsProvider;
        $this->identifiableObjectHookByFormTypeProvider = $identifiableObjectHookByFormTypeProvider;
        $this->hookInformationProvider = $hookInformationProvider;
        $this->hookDescriptionGenerator = $hookDescriptionGenerator;
        $this->serviceIds = $serviceIds;
        $this->optionFormHookNames = $optionFormHookNames;
        $this->formTypes = $formTypes;
        $this->sqlUpgradePath = $sqlUpgradePath;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:update:sql-upgrade-file-hooks-listing')
            ->setDescription(
                'Adds sql to sql upgrade file which contains hook insert operation'
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
        $this->initContext();

        $io = new SymfonyStyle($input, $output);

        if (!in_array($this->env, ['dev', 'test'])) {
            $io->warning('Dev or test environment is required to fully list all the hooks');

            return 1;
        }

        $hookNames = $this->getHookNames();
        $hookNames = $this->getWithoutRegisteredHooks($hookNames);

        if (empty($hookNames)) {
            $io->note('No hooks found.');

            return 0;
        }

        $hookDescriptions = $this->getHookDescriptions($hookNames);

        try {
            $sqlUpgradeFile = $this->getSqlUpgradeFileByPrestaShopVersion($input->getArgument('ps-version'));
        } catch (FileNotFoundException $exception) {
            $io->error($exception->getMessage());

            return 1;
        }
        if (empty($sqlUpgradeFile)) {
            return 1;
        }

        $sqlInsertStatement = $this->getSqlInsertStatement($hookDescriptions);

        $this->appendSqlToFile($sqlUpgradeFile, $sqlInsertStatement);

        $io->success(
            sprintf(
                'All %s hooks have been listed to file %s',
                count($hookNames),
                $sqlUpgradeFile
            )
        );

        return 0;
    }

    /**
     * Initialize PrestaShop Context
     */
    private function initContext()
    {
        //We need to have an employee or the listing hooks don't work
        //see LegacyHookSubscriber
        if (!$this->legacyContext->getContext()->employee) {
            //Even a non existing employee is fine
            $this->legacyContext->getContext()->employee = new Employee();
        }
    }

    /**
     * Gets all hooks names which need to be appended.
     *
     * @return string[]
     */
    private function getHookNames()
    {
        $gridDefinitionHookNames = $this->gridDefinitionHookByServiceIdsProvider->getHookNames($this->serviceIds);

        $identifiableObjectHookNames = $this->identifiableObjectHookByFormTypeProvider->getHookNames($this->formTypes);

        return array_merge(
            $identifiableObjectHookNames,
            $this->optionFormHookNames,
            $gridDefinitionHookNames
        );
    }

    /**
     * Gets sql upgrade file by PrestaShop version.
     *
     * @param string $version
     *
     * @return string
     */
    private function getSqlUpgradeFileByPrestaShopVersion($version)
    {
        $sqlUpgradeFile = $this->sqlUpgradePath . $version . '.sql';

        if (!file_exists($sqlUpgradeFile)) {
            throw new FileNotFoundException(sprintf('File %s has not been found', $sqlUpgradeFile));
        }

        return $sqlUpgradeFile;
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
        $registeredHooks = $this->hookInformationProvider->getHooks();
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
        $descriptions = [];
        foreach ($hookNames as $hookName) {
            $hookDescription = $this->hookDescriptionGenerator->generate($hookName);

            $descriptions[] = $hookDescription;
        }

        return $descriptions;
    }
}
