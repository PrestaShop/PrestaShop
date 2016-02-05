<?php
/**
 * 2007-2015 PrestaShop
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
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Refresh Sf2 cache
 */
class Refresh
{
    private $env;
    private $application;
    private $output;
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
        $this->env = $env;
        $this->output = new NullOutput();
        $this->commands = [];

        require_once _PS_ROOT_DIR_.'/app/AppKernel.php';
        $kernel = new \AppKernel($this->env, false);
        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);
    }

    /**
     * add cache clear
     */
    public function addCacheClear()
    {
        $this->commands[] = ['command' => 'cache:clear', '--env' => $this->env, '--no-debug' => true];
    }

    /**
     * add assetic dump
     */
    public function addAsseticDump()
    {
        $this->commands[] = ['command' => 'assetic:dump', '--env' => $this->env, '--no-debug' => true];
    }

    /**
     * add doctrine schema update
     */
    public function addDoctrineSchemaUpdate()
    {
        $this->commands[] = ['command' => 'doctrine:schema:update', '--env' => $this->env, '--no-debug' => true, '--force' => true];
    }

    /**
     * Execute all defined commands
     *
     * @throws \Exception if no command defined
     */
    public function execute()
    {
        if (empty($this->commands)) {
            throw new \Exception('Error, you need to define at least on command');
        }

        foreach ($this->commands as $command) {
            $this->application->run(new ArrayInput($command), $this->output);
        }
    }
}
