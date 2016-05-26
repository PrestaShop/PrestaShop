<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Service\Cache;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Refresh Sf2 cache.
 */
class Refresh
{
    private $env;
    private $application;
    private $commands;

    /**
     * Constructor.
     *
     * Construct SF2 env
     *
     * @param string $env prod|dev
     */
    public function __construct($env = 'prod')
    {
        umask(0000);
        set_time_limit(0);
        $this->env = _PS_MODE_DEV_ ? 'dev' : 'prod';
        $this->commands = [];

        require_once _PS_ROOT_DIR_.'/app/AppKernel.php';
        $kernel = new \AppKernel($this->env, false);
        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);
    }

    /**
     * Add cache clear.
     *
     * @param string $env Environment to clear
     */
    public function addCacheClear($env = 'dev')
    {
        $this->commands[] = ['command' => 'cache:clear', '--env' => $env, '--no-debug' => true];
    }

    /**
     * Add doctrine schema update.
     */
    public function addDoctrineSchemaUpdate()
    {
        $this->commands[] = ['command' => 'doctrine:schema:update', '--env' => $this->env, '--no-debug' => false, '--force' => true];
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
            throw new \Exception('Error, you need to define at least on command');
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
}
