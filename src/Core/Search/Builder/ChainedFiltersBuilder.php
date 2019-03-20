<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;


use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * This builder is used to compose other builders, it iterates through its builders
 * set the config for all of them (each one can pick its own parameters), and when
 * building iterates through them overriding the same Filters instance step by step.
 *
 * This allows to split every Filters building into separate classes and then compose
 * them based on your needs.
 */
class ChainedFiltersBuilder extends AbstractFiltersBuilder
{
    /** @var FiltersBuilderCollection */
    private $builders;

    /**
     * @param array $builders Array of FiltersBuilderInterface
     *
     * @throws \PrestaShop\PrestaShop\Core\Exception\TypeException
     */
    public function __construct(array $builders = [])
    {
        $this->builders = new FiltersBuilderCollection($builders);
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config)
    {
        parent::setConfig($config);

        /** @var FiltersBuilderInterface $builder */
        foreach ($this->builders as $builder) {
            $builder->setConfig($config);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function buildFilters(Filters $filters = null)
    {
        /** @var FiltersBuilderInterface $builder */
        foreach ($this->builders as $builder) {
            $filters = $builder->buildFilters($filters);
        }

        return $filters;
    }

}
