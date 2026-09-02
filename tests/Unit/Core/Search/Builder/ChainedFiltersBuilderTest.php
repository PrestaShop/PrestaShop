<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public function buildFilters(?Filters $filters = null)
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
