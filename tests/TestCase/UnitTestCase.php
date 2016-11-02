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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\tests\TestCase;

use Cache;
use Configuration;
use Context;
use Db;
use PHPUnit_Framework_TestCase;
use PrestaShop\PrestaShop\Core\ContainerBuilder;
use \PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use Phake;
use Symfony\Component\HttpKernel\Kernel;
use PrestaShop\PrestaShop\Tests\TestCase\FakeEntityMapper;
use PrestaShop\PrestaShop\Tests\TestCase\FakeConfiguration;
use Symfony\Component\HttpFoundation\Request;

class UnitTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var FakeEntityMapper
     */
    public $entity_mapper;

    /**
     * @var \ContextCore
     */
    public $context;

    /**
     * @var Db
     */
    public $database;

    /**
     * @var Cache
     */
    public $cache;

    /**
     * @var Kernel
     */
    public $sfKernel;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \ToolsCore
     */
    protected $tools;

    /**
     * @param null $mock
     * @return Db|mixed
     */
    public function setupDatabaseMock($mock = null)
    {
        if (is_null($mock)) {
            $this->database = Phake::mock('Db');
        } else {
            $this->database = $mock;
        }
        Db::setInstanceForTesting($this->database);

        return $this->database;
    }

    public function setup()
    {
        $this->container = new Container();
        ServiceLocator::setServiceContainerInstance($this->container);

        $this->setupDatabaseMock();

        $this->entity_mapper = new FakeEntityMapper();

        $this->container->bind('\\PrestaShop\\PrestaShop\\Adapter\\EntityMapper', $this->entity_mapper);

        $this->context = Phake::mock('Context');
        Phake::when($this->context)->getTranslator()->thenReturn(
            Phake::mock('\Symfony\Component\Translation\Translator')
        );

        Phake::when($this->context)->cloneContext()->thenReturn($this->context);

        $this->context->shop = Phake::mock('Shop');
        Context::setInstanceForTesting($this->context);

        $this->cache = Phake::mock('Cache');
        Cache::setInstanceForTesting($this->cache);

        $this->setupContextualTemplateEngineMock();
        $this->setupContextualLanguageMock();
        $this->setupContextualEmployeeMock();
        $this->setupContextualCookieMock();
        $this->setupRequestMock();
    }

    protected function setupContextualTemplateEngineMock()
    {
       $this->context->smarty = Phake::mock('Smarty');

       return $this->context->smarty;
    }

    protected function setupContextualEmployeeMock()
    {
        $this->context->employee = Phake::mock('Employee');

        return $this->context->employee;
    }

    protected function setupContextualLanguageMock()
    {
        $this->context->language = Phake::mock('Language');

        return $this->context->language;
    }

    protected function setupContextualCookieMock() {
        $this->context->cookie = Phake::mock('Cookie');

        return $this->context->cookie;
    }

    protected function setupRequestMock()
    {
        $this->request = Request::createFromGlobals();
        $this->tools = new \Tools($this->request);
    }

    public function setConfiguration(array $keys)
    {
        $fakeConfiguration = new FakeConfiguration($keys);
        $this->container->bind(
            '\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface',
            $fakeConfiguration
        );
        return $fakeConfiguration;
    }

    public function setupSfKernel()
    {
        require_once __DIR__.'/../../app/autoload.php';
        require_once __DIR__.'/../../app/AppKernel.php';
        $this->sfKernel = new \AppKernel('test', true);
        $this->sfKernel->loadClassCache();
        $this->sfKernel->boot();

        return $this->sfKernel;
    }

    public function teardown()
    {
        Cache::deleteTestingInstance();
        Db::deleteTestingInstance();
        Context::deleteTestingInstance();
        /**
         * @todo proxy static calls inside Configuration to a mockable instance
         * so that Configuration can be (indirectly) mocked.
         * This way we'll avoid doing obscure teardown stuff like below.
         */
        Configuration::clearConfigurationCacheForTesting();

        $container_builder = new ContainerBuilder();
        $container = $container_builder->build();
        ServiceLocator::setServiceContainerInstance($container);
    }
}
