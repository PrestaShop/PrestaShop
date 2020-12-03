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

namespace Tests\Unit\Core\Search\Builder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Builder\AbstractFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Builder\ChainedFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;

class ChainedFiltersBuilderTest extends TestCase
{
    public function testWithoutBuilders()
    {
        $builder = new ChainedFiltersBuilder();
        $filters = $builder->buildFilters();
        $this->assertNull($filters);
    }

    public function testWithBuilders()
    {
        $limitBuilder = new ConfigurableFiltersBuilder(['limit' => 'limit']);
        $offsetBuilder = new ConfigurableFiltersBuilder(['offset' => 'offset']);
        $builder = new ChainedFiltersBuilder([$limitBuilder, $offsetBuilder]);

        $builder->setConfig(['limit' => 10, 'offset' => 20]);
        $filters = $builder->buildFilters();
        $this->assertNotNull($filters);
        $this->assertEquals(['limit' => 10, 'offset' => 20], $filters->all());
    }

    public function testSequentialOverride()
    {
        $limitABuilder = new ConfigurableFiltersBuilder(['limit_a' => 'limit']);
        $limitBBuilder = new ConfigurableFiltersBuilder(['limit_b' => 'limit']);

        $builder = new ChainedFiltersBuilder([$limitABuilder, $limitBBuilder]);
        $builder->setConfig(['limit_a' => 10, 'limit_b' => 20]);
        $filters = $builder->buildFilters();
        $this->assertEquals(['limit' => 20], $filters->all());

        $builder = new ChainedFiltersBuilder([$limitBBuilder, $limitABuilder]);
        $builder->setConfig(['limit_a' => 10, 'limit_b' => 20]);
        $filters = $builder->buildFilters();
        $this->assertEquals(['limit' => 10], $filters->all());
    }
}

/**
 * Class ConfigurableFiltersBuilder used for test, pick parameters from the config
 * array and is able to rename it, this is used to check the sequential override system.
 *
 * E.g:
 *  If $managedParameters = ['limit_admin' => 'limit']
 *  Then $config = ['limit_admin' => 10] will result in $parameters['limit' => 10]
 */
class ConfigurableFiltersBuilder extends AbstractFiltersBuilder
{
    /** @var array */
    private $parameters;

    /** @var array */
    private $managedParameters;

    /**
     * @param array $managedParameters
     */
    public function __construct(array $managedParameters)
    {
        $this->managedParameters = $managedParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        $this->parameters = [];
        foreach ($config as $key => $value) {
            if (isset($this->managedParameters[$key])) {
                $parameterName = $this->managedParameters[$key];
                $this->parameters[$parameterName] = $value;
            }
        }

        return parent::setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilters(Filters $filters = null)
    {
        if (empty($this->parameters)) {
            return $filters;
        }

        if (null === $filters) {
            $filters = new Filters($this->parameters);
        } else {
            $filters->add($this->parameters);
        }

        return $filters;
    }
}
