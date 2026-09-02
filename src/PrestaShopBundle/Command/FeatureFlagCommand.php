<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShopBundle\Entity\FeatureFlag;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This CLI command allows to enable/disable a feature flag or to list all of them.
 */
class FeatureFlagCommand extends Command
{
    private const SUCCESS_RETURN_CODE = 0;
    private const INVALID_ARGUMENTS_RETURN_CODE = 1;

    protected static $defaultName = 'prestashop:feature-flag';

    /**
     * @var string[]
     */
    private $allowedActions = [
        'enable',
        'disable',
        'list',
    ];

    public function __construct(
        private readonly FeatureFlagRepository $featureFlagRepository,
        private readonly FeatureFlagManager $featureFlagManager
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Manage feature flags via command line')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)))
            ->addArgument('feature_flag', InputArgument::OPTIONAL, 'Feature flag you want to enable/disable')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('action');
        if (!in_array($action, $this->allowedActions)) {
            $output->writeln(sprintf(
                '<error>Unknown action, it must be one of the following values: %s</error>',
                implode('/', $this->allowedActions)
            ));

            return self::INVALID_ARGUMENTS_RETURN_CODE;
        }

        if ($action === 'list') {
            return $this->listFeatureFlags($output);
        } else {
            return $this->toggleFeatureFlag('enable' === $action, $input, $output);
        }
    }

    private function listFeatureFlags(OutputInterface $output): int
    {
        $featureFlags = $this->featureFlagRepository->findAll();
        $table = new Table($output);
        $table->setHeaders(['Feature flag', 'Type', 'State']);
        /** @var FeatureFlag $featureFlag */
        foreach ($featureFlags as $featureFlag) {
            $table->addRow([
                $featureFlag->getName(),
                $this->getTypeRow($featureFlag),
                $this->featureFlagManager->isEnabled($featureFlag->getName()) ? 'Enabled' : 'Disabled',
            ]);
        }
        $table->render();

        return self::SUCCESS_RETURN_CODE;
    }

    private function getTypeRow(FeatureFlag $featureFlag): string
    {
        $out = [];

        foreach ($featureFlag->getOrderedTypes() as $type) {
            if ($this->featureFlagManager->getUsedType($featureFlag->getName()) === $type) {
                $out[] = "[$type]";
            } else {
                $out[] = $type;
            }
        }

        return implode(',', $out);
    }

    private function toggleFeatureFlag(bool $expectedState, InputInterface $input, OutputInterface $output): int
    {
        $featureFlagArgument = $input->getArgument('feature_flag');
        if (empty($featureFlagArgument)) {
            $output->writeln('<error>You must specify the feature_flag argument for this action</error>');

            return self::INVALID_ARGUMENTS_RETURN_CODE;
        }

        $featureFlag = $this->featureFlagRepository->getByName($featureFlagArgument);
        if ($featureFlag === null) {
            $output->writeln(sprintf(
                '<error>Feature flag %s does not exist</error>',
                $featureFlagArgument
            ));

            return self::INVALID_ARGUMENTS_RETURN_CODE;
        }

        if ($expectedState) {
            $this->featureFlagManager->enable($featureFlagArgument);
            $output->writeln(sprintf(
                '<info>Feature flag %s was enabled</info>',
                $featureFlagArgument
            ));
        } else {
            $this->featureFlagManager->disable($featureFlagArgument);
            $output->writeln(sprintf(
                '<info>Feature flag %s was disabled</info>',
                $featureFlagArgument
            ));
        }

        return self::SUCCESS_RETURN_CODE;
    }
}
