<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public $type;

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
            'type' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 64,
                'default' => FeatureFlagSettings::TYPE_DEFAULT,
            ],
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
