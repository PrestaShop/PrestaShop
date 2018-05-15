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

namespace Tests\TestCase;

use Cache;
use Configuration;
use Context;
use Db;
use PrestaShop\PrestaShop\Core\ContainerBuilder;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use Phake;
use Symfony\Component\HttpKernel\Kernel;
use Tests\TestCase\FakeEntityMapper;
use Tests\TestCase\FakeConfiguration;
use Symfony\Component\HttpFoundation\Request;

class UnitTestCase extends \PHPUnit\Framework\TestCase
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

    protected function setUp()
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
        $this->context->controller = new \stdClass;
        Context::setInstanceForTesting($this->context);

        $this->cache = Phake::mock('Cache');
        Cache::setInstanceForTesting($this->cache);

        $this->setupContextualTemplateEngineMock();
        $this->setupContextualLanguageMock();
        $this->setupContextualLinkMock();
        $this->setupContextualEmployeeMock();
        $this->setupContextualCookieMock();
        $this->setupContextualCurrencyMock();
        $this->setupRequestMock();
        if (!defined('_PS_TAB_MODULE_LIST_URL_')) {
            define('_PS_TAB_MODULE_LIST_URL_', '');
        }
    }

    protected function setupContextualTemplateEngineMock()
    {
       $this->context->smarty = Phake::mock('Smarty');

       return $this->context->smarty;
    }

    protected function setupContextualCurrencyMock()
    {
        $this->context->currency = Phake::mock('Currency');

        return $this->context->currency;
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

    protected function setupContextualLinkMock()
    {
        $this->context->link = Phake::mock('Link');

        return $this->context->link;
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
        require_once __DIR__.'/../../vendor/autoload.php';
        require_once __DIR__.'/../../app/AppKernel.php';
        $this->sfKernel = new \AppKernel('test', true);
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

    /**
    * Call protected/private method of a class.
    *
    * @param object &$object    Instantiated object that we will run method on.
    * @param string $methodName Method name to call
    * @param array  $parameters Array of parameters to pass into method.
    *
    * @return mixed Method return.
    * @link https://jtreminio.com/2013/03/unit-testing-tutorial-part-3-testing-protected-private-methods-coverage-reports-and-crap/
    */
    protected function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
