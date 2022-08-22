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

use DOMDocument;
use Employee;
use Exception;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Hook\Generator\HookDescriptionGenerator;
use PrestaShop\PrestaShop\Core\Hook\HookDescription;
use PrestaShop\PrestaShop\Core\Hook\Provider\GridDefinitionHookByServiceIdsProvider;
use PrestaShop\PrestaShop\Core\Hook\Provider\IdentifiableObjectHookByFormTypeProvider;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command is used for appending the hook names in the configuration file.
 */
class AppendConfigurationFileHooksListCommand extends Command
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
    private $hookFile;

    public function __construct(
        string $env,
        LegacyContext $legacyContext,
        GridDefinitionHookByServiceIdsProvider $gridDefinitionHookByServiceIdsProvider,
        IdentifiableObjectHookByFormTypeProvider $identifiableObjectHookByFormTypeProvider,
        HookDescriptionGenerator $hookDescriptionGenerator,
        array $serviceIds,
        array $optionFormHookNames,
        array $formTypes,
        string $hookFile
    ) {
        parent::__construct();
        $this->env = $env;
        $this->legacyContext = $legacyContext;
        $this->gridDefinitionHookByServiceIdsProvider = $gridDefinitionHookByServiceIdsProvider;
        $this->identifiableObjectHookByFormTypeProvider = $identifiableObjectHookByFormTypeProvider;
        $this->hookDescriptionGenerator = $hookDescriptionGenerator;
        $this->serviceIds = $serviceIds;
        $this->optionFormHookNames = $optionFormHookNames;
        $this->formTypes = $formTypes;
        $this->hookFile = $hookFile;
    }

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
        $this->initContext();

        $io = new SymfonyStyle($input, $output);

        if (!in_array($this->env, ['dev', 'test'])) {
            $io->warning('Dev or test environment is required to fully list all the hooks');

            return 1;
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

            return 0;
        }

        $io->note('No new hooks have been added to configuration file');

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
        if (!file_exists($this->hookFile)) {
            throw new Exception(sprintf('File %s has not been found', $this->hookFile));
        }

        $hookFileContent = file_get_contents($this->hookFile);

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

        $xmlContent = $xmlFileContent->asXML();
        if (empty($xmlContent)) {
            throw new Exception(sprintf('Failed to save new xml content to file %s', $this->hookFile));
        }

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlContent);

        $formattedXMLContent = $dom->saveXML();
        file_put_contents($this->hookFile, $formattedXMLContent);

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
        $descriptions = [];
        foreach ($hookNames as $hookName) {
            $hookDescription = $this->hookDescriptionGenerator->generate($hookName);

            $descriptions[] = $hookDescription;
        }

        return $descriptions;
    }
}
