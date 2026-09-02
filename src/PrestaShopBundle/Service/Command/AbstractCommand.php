<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Service\Command;

use AdminKernel;
use AppKernel;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class AbstractCommand
{
    protected $kernel;
    protected $application;
    protected $commands = [];

    /**
     * Constructor.
     *
     * Construct the symfony environment.
     *
     * @param AppKernel $kernel Symfony Kernel
     */
    public function __construct(?AppKernel $kernel = null)
    {
        set_time_limit(0);

        if (null === $kernel) {
            global $kernel;

            if (null === $kernel) {
                require_once _PS_ROOT_DIR_ . '/app/AdminKernel.php';
                $kernel = new AdminKernel(_PS_ENV_, _PS_MODE_DEV_);
            }
        }

        $this->kernel = $kernel;
        $this->application = new Application($this->kernel);
        $this->application->setAutoExit(false);
    }

    /**
     * Execute all defined commands.
     *
     * @throws Exception if no command defined
     */
    public function execute()
    {
        $bufferedOutput = new BufferedOutput();
        $commandOutput = [];

        if (empty($this->commands)) {
            throw new Exception('Error, you need to define at least one command');
        }

        foreach ($this->commands as $command) {
            $exitCode = $this->application->run(new ArrayInput($command), $bufferedOutput);

            $commandOutput[$command['command']] = [
                'exitCode' => $exitCode,
                'output' => $bufferedOutput->fetch(),
            ];
        }

        return $commandOutput;
    }

    /**
     * Add cache:clear to the execution.
     */
    public function addCacheClear()
    {
        $this->commands[] = [
            'command' => 'doctrine:cache:clear-metadata',
            '--flush' => true,
        ];

        $this->commands[] = [
            'command' => 'doctrine:cache:clear-query',
            '--flush' => true,
        ];

        $this->commands[] = [
            'command' => 'doctrine:cache:clear-result',
            '--flush' => true,
        ];

        $this->commands[] = [
            'command' => 'cache:clear',
            '--no-warmup' => true,
        ];
    }
}
