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

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\FeatureValueGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

class FeatureValueFilters extends Filters
{
    /** @var string */
    protected $filterId = FeatureValueGridDefinitionFactory::GRID_ID;

    /**
     * @var int
     */
    protected $featureId;

    /**
     * @var int
     */
    protected $languageId;

    public function __construct(array $filters = [])
    {
        if (!isset($filters['filters']['feature_id'])) {
            throw new InvalidArgumentException(sprintf('%s filters expect a feature_id filter', static::class));
        }

        if (!isset($filters['filters']['language_id'])) {
            throw new InvalidArgumentException(sprintf('%s filters expect a language_id filter', static::class));
        }

        $this->featureId = (int) $filters['filters']['feature_id'];
        $this->languageId = (int) $filters['filters']['language_id'];

        parent::__construct($filters, $this->filterId);
    }

    public function getFeatureId(): int
    {
        return $this->featureId;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => self::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => 'value',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
