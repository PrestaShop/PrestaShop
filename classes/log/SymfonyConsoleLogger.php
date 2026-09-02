<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

use Symfony\Component\Console\Output\OutputInterface;

/**
 * This logger class respects the PrestaShopLoggerInterface but is based on the Symfony console
 * component. It is used as a temporary solution in legacy code until we can replace the usage of
 * the legacy interface.
 */
class SymfonyConsoleLogger extends AbstractLogger
{
    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output, $level = self::INFO)
    {
        parent::__construct($level);
        $this->output = $output;
    }

    protected function logMessage($message, $level)
    {
        $this->output->writeln($message);
    }
}
