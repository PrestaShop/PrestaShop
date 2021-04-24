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

namespace PrestaShopBundle\Service\Command;

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
    public function __construct(AppKernel $kernel = null)
    {
        set_time_limit(0);

        if (null === $kernel) {
            global $kernel;

            if (null === $kernel) {
                require_once _PS_ROOT_DIR_ . '/app/AppKernel.php';
                $kernel = new AppKernel(_PS_ENV_, _PS_MODE_DEV_);
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
