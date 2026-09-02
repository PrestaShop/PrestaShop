<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            'orderBy' => 'position',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
