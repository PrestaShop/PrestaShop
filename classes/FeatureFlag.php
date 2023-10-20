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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;

/**
 * Class FeatureFlagCore even if the feature flag is mostly handled via its Doctrine entity, we need this legacy class
 * to handle the data installation for this entity.
 */
class FeatureFlagCore extends ObjectModel
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $label_wording;

    /**
     * @var string
     */
    public $label_domain;

    /**
     * @var string
     */
    public $description_wording;

    /**
     * @var string
     */
    public $description_domain;

    /**
     * @var bool
     */
    public $state = false;

    /**
     * @var string
     */
    public $stability;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'feature_flag',
        'primary' => 'id_feature_flag',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 191],
            'label_wording' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 191],
            'label_domain' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 255],
            'description_wording' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 191],
            'description_domain' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 255],
            'state' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'stability' => [
                'type' => self::TYPE_STRING,
                'size' => 64,
                'values' => [
                    FeatureFlagSettings::STABILITY_STABLE,
                    FeatureFlagSettings::STABILITY_BETA,
                ],
                'default' => FeatureFlagSettings::STABILITY_BETA,
            ],
        ],
    ];

    public static function isEnabled(string $name): bool
    {
        $query = sprintf(
            "SELECT state FROM %sfeature_flag WHERE name = '%s'",
            _DB_PREFIX_,
            pSQL($name)
        );

        return (bool) Db::getInstance()->getValue($query);
    }
}
