<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Service\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Input\ArrayInput;

abstract class AbstractCommand
{
    private $env;
    protected $application;
    protected $commands;

    /**
     * Constructor.
     *
     * Construct the symfony environment.
     *
     * @param string $env Environment to set.
     */
    public function __construct($env = null)
    {
        umask(0000);
        set_time_limit(0);

        if (null === $env) {
            $this->env = _PS_MODE_DEV_ ? 'dev' : 'prod';
        } else {
            $this->env = $env;
        }

        $this->commands = array();

        require_once _PS_ROOT_DIR_.'/app/AppKernel.php';

        $kernel = new \AppKernel($this->env, false);
        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);
    }

    /**
     * Execute all defined commands.
     *
     * @throws \Exception if no command defined
     */
    public function execute()
    {
        $bufferedOutput = new BufferedOutput();
        $commandOutput = array();

        if (empty($this->commands)) {
            throw new \Exception('Error, you need to define at least one command');
        }

        foreach ($this->commands as $command) {
            $exitCode = $this->application->run(new ArrayInput($command), $bufferedOutput);

            $commandOutput[$command['command']] = array(
                'exitCode' => $exitCode,
                'output' => $bufferedOutput->fetch(),
            );
        }

        return $commandOutput;
    }

    /**
     * Add cache:clear to the execution.
     */
    public function addCacheClear()
    {
        $this->commands[] = array(
            'command' => 'doctrine:cache:clear-metadata',
            '--flush' => true,
        );

        $this->commands[] = array(
            'command' => 'doctrine:cache:clear-query',
            '--flush' => true,
        );

        $this->commands[] = array(
            'command' => 'doctrine:cache:clear-result',
            '--flush' => true,
        );

        $this->commands[] = array(
            'command' => 'cache:clear',
            '--no-warmup' => true,
        );
    }
}
