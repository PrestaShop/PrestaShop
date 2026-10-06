<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cleans the CSP violation log: deletes reports older than the per-shop retention setting
 * (PS_CSP_RETENTION_DAYS, or the --older-than override) and enforces the per-shop row cap. Schedule
 * it from system cron. Allowed sources are never deleted.
 */
#[AsCommand(
    name: 'prestashop:csp:prune-log',
    description: 'Prune the CSP violation log: delete reports past their retention and enforce the row cap.',
)]
final class PruneCspLogCommand extends Command
{
    public function __construct(
        private readonly CspLogRepository $cspLogRepository,
        private readonly CspViolationRecorder $cspViolationRecorder,
        private readonly ShopConfigurationInterface $configuration,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('shop', null, InputOption::VALUE_REQUIRED, 'Only prune this shop id (default: every shop with logs).')
            ->addOption('older-than', null, InputOption::VALUE_REQUIRED, 'Delete reports older than this many days, overriding each shop\'s retention setting.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shopOption = $input->getOption('shop');
        $olderThanOption = $input->getOption('older-than');

        if (null !== $olderThanOption && (!ctype_digit((string) $olderThanOption))) {
            $output->writeln('<error>--older-than must be a positive number of days.</error>');

            return self::INVALID;
        }

        $overrideDays = null !== $olderThanOption ? (int) $olderThanOption : null;
        $now = new DateTimeImmutable();

        // Visit each storefront shop with logs, plus the single global back-office surface.
        $targets = [];
        if (null !== $shopOption) {
            $targets[] = [CspContext::FRONT, (int) $shopOption];
        } else {
            foreach ($this->cspLogRepository->distinctShopIds(CspContext::FRONT) as $shopId) {
                $targets[] = [CspContext::FRONT, $shopId];
            }
            foreach ($this->cspLogRepository->distinctShopIds(CspContext::ADMIN) as $shopId) {
                $targets[] = [CspContext::ADMIN, $shopId];
            }
        }

        foreach ($targets as [$context, $shopId]) {
            $days = $overrideDays ?? $this->retentionDays($context, $shopId);

            $deletedByAge = 0;
            if ($days > 0) {
                $deletedByAge = $this->cspLogRepository->deleteOlderThanByShop($context, $shopId, $now->modify(sprintf('-%d days', $days)));
            }

            $this->cspViolationRecorder->enforceRowCap($context, $shopId);

            $output->writeln(sprintf(
                '%s%s: deleted %d report(s) older than %d day(s); row cap enforced.',
                CspContext::ADMIN === $context ? 'Back office' : 'Shop ',
                CspContext::ADMIN === $context ? '' : (string) $shopId,
                $deletedByAge,
                $days
            ));
        }

        return self::SUCCESS;
    }

    private function retentionDays(CspContext $context, int $shopId): int
    {
        return CspContext::ADMIN === $context
            ? (int) $this->configuration->get('PS_CSP_ADMIN_RETENTION_DAYS', 0)
            : (int) $this->configuration->get('PS_CSP_RETENTION_DAYS', 0, ShopConstraint::shop($shopId));
    }
}
