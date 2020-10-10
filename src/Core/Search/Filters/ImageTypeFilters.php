<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ImageTypeGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Default filters for image type grid.
 */
final class ImageTypeFilters extends Filters
{
    /**
     * @var string
     */
    protected $filterId = ImageTypeGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_image_type',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
