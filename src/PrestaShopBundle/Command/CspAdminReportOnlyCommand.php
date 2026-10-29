<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Recovery hatch: forces the back-office CSP back to report-only, so a merchant who enforced a broken
 * admin policy and locked themselves out of the page that fixes it can recover from the CLI without
 * database access. (_PS_CSP_ADMIN_DISABLE_ in config/defines_custom.inc.php disables it outright.)
 */
#[AsCommand(
    name: 'prestashop:csp:admin-report-only',
    description: 'Force the back-office Content Security Policy back to report-only (lockout recovery).',
)]
final class CspAdminReportOnlyCommand extends Command
{
    public function __construct(
        private readonly DataConfigurationInterface $adminConfiguration,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Keep the other settings as they are; only force report-only back on (never blocked by the
        // enforce guard, which only fences turning enforcement on).
        $configuration = $this->adminConfiguration->getConfiguration();
        $configuration['report_only'] = true;
        $this->adminConfiguration->updateConfiguration($configuration);

        $output->writeln('<info>Back-office CSP is now report-only; it no longer blocks. Re-enforce it from the back office once the policy is fixed.</info>');

        return self::SUCCESS;
    }
}
