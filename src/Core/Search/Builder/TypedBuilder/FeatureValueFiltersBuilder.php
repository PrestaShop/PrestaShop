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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder;

use PrestaShop\PrestaShop\Core\Search\Builder\AbstractFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureValueFilters;
use Symfony\Component\HttpFoundation\Request;

class FeatureValueFiltersBuilder extends AbstractFiltersBuilder implements TypedFiltersBuilderInterface
{
    /** @var Request */
    private $request;

    /**
     * @var int
     */
    private $contextLangId;

    public function __construct(
        int $contextLangId
    ) {
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config): FeatureValueFiltersBuilder
    {
        $this->request = $config['request'] ?? null;

        return parent::setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilters(Filters $filters = null)
    {
        $filterParameters = FeatureValueFilters::getDefaults();
        if (null !== $filters) {
            $filterParameters = array_replace($filterParameters, $filters->all());
        }

        $filterParameters['filters']['feature_id'] = $this->getFeatureId();
        $filterParameters['filters']['language_id'] = $this->contextLangId;

        return new FeatureValueFilters($filterParameters);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(string $filterClassName): bool
    {
        return $filterClassName === FeatureValueFilters::class;
    }

    private function getFeatureId(): int
    {
        return $this->request->attributes->getInt('featureId');
    }
}
