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

use Doctrine\ORM\NoResultException;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\AddApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\DeleteApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\ForceApiClientSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\CreatedApiClient;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractorInterface;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command is used to manage API Clients, for starter it will only handle creating and deleting an
 * API Client but some additional actions can be added in the future (regenerate secret, toggle status,
 * listing, ...)
 */
#[AsCommand(name: 'prestashop:api-client', description: 'Manage API Client.', )]
class APIClientCommand extends Command
{
    private const ACTIONS = [
        'create',
        'delete',
    ];

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly ApiResourceScopesExtractorInterface $apiResourceScopesExtractor,
        private readonly ApiClientRepository $apiClientRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create an API Client to use the API')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('The action to perform possible values are: %s', implode(',', self::ACTIONS)))
            ->addArgument('client-id', InputArgument::REQUIRED, 'Client ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Client Name')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'Client Description', 'Created by CLI command.')
            ->addOption('all-scopes', null, InputOption::VALUE_NONE, 'Automatically assign all available scopes')
            ->addOption('scopes', null, InputOption::VALUE_REQUIRED, 'Client list of scopes separated by commas', [])
            ->addOption('secret-only', null, InputOption::VALUE_NONE, 'Only output secret value after creation')
            ->addOption('timeout', null, InputOption::VALUE_REQUIRED, 'Timeout in seconds (default: 3600)', 3600)
            ->addOption('secret', null, InputOption::VALUE_OPTIONAL, 'Force secret value (default: auto generated)', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $action = $input->getArgument('action');

        switch ($action) {
            case 'create':
                return $this->createAPIClient($input, $io);
            case 'delete':
                return $this->deleteAPIClient($input, $io);
        }

        $io->error('Unknown action ' . $action);

        return Command::FAILURE;
    }

    private function createAPIClient(InputInterface $input, SymfonyStyle $io): int
    {
        $clientId = $input->getArgument('client-id');
        if (empty($clientId)) {
            $io->error('Client ID cannot be empty');

            return Command::FAILURE;
        }

        $clientName = $input->getOption('name');
        if (empty($clientName)) {
            $clientName = $clientId;
        }

        if ($input->getOption('all-scopes')) {
            $allScopes = $this->apiResourceScopesExtractor->getAllApiResourceScopes();
            $clientScopes = [];

            foreach ($allScopes as $apiResourceScopes) {
                $clientScopes = array_merge($clientScopes, $apiResourceScopes->getScopes());
            }
        } else {
            $clientScopes = [];
            $inputScopes = $input->getOption('scopes') ?? '';
            if (!empty($inputScopes)) {
                $clientScopes = array_map(fn (string $scope) => trim($scope), explode(',', $inputScopes));
            }
        }

        $clientDescription = $input->getOption('description');
        $clientTimeout = $input->getOption('timeout');

        $command = new AddApiClientCommand(
            $clientName,
            $clientId,
            true,
            $clientDescription,
            $clientTimeout,
            $clientScopes,
        );

        /** @var CreatedApiClient $createdApiClient */
        $createdApiClient = $this->commandBus->handle($command);
        $clientSecret = $createdApiClient->getSecret();

        $forcedSecret = $input->getOption('secret');
        if (!empty($forcedSecret)) {
            $command = new ForceApiClientSecretCommand(
                $createdApiClient->getApiClientId()->getValue(),
                $forcedSecret,
            );
            $this->commandBus->handle($command);
            $clientSecret = $forcedSecret;
        }

        if ($input->getOption('secret-only')) {
            $io->writeln($clientSecret);
        } else {
            $io->success('Successfully create a new API Client #' . $createdApiClient->getApiClientId()->getValue());
            $io->table(
                ['Client field', 'Value'],
                [
                    ['Client ID', $clientId],
                    ['Client secret', $clientSecret],
                    ['Name', $clientName],
                    ['Description', $clientDescription],
                    ['Timeout', $clientTimeout],
                    ['Scopes', empty($clientScopes) ? 'None' : implode(', ', $clientScopes)],
                ],
            );
        }

        return Command::SUCCESS;
    }

    private function deleteAPIClient(InputInterface $input, SymfonyStyle $io): int
    {
        $clientId = $input->getArgument('client-id');
        if (empty($clientId)) {
            $io->error('Client ID cannot be empty');

            return Command::FAILURE;
        }

        try {
            $apiClient = $this->apiClientRepository->getByClientId($clientId);
        } catch (NoResultException) {
            $io->error('API Client with Client ID ' . $clientId . ' not found');

            return Command::FAILURE;
        }

        $command = new DeleteApiClientCommand($apiClient->getId());
        $this->commandBus->handle($command);

        $io->success('Successfully removed API Client ' . $clientId);

        return Command::SUCCESS;
    }
}
