<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\FeatureFlag;

class FeatureFlagSettings
{
    /**
     * Stability consts
     */
    public const STABILITY_STABLE = 'stable';
    public const STABILITY_BETA = 'beta';

    /**
     * Type consts
     */
    public const TYPE_DEFAULT = 'env,dotenv,db';
    public const TYPE_ENV = 'env';
    public const TYPE_QUERY = 'query';
    public const TYPE_DOTENV = 'dotenv';
    public const TYPE_DB = 'db';

    /**
     * Prefix for DotEnv & Env Layers
     */
    public const PREFIX = 'PS_FF_';

    public const FEATURE_FLAG_ADMIN_API_MULTISTORE = 'admin_api_multistore';
    public const FEATURE_FLAG_ADMIN_API_EXPERIMENTAL_ENDPOINTS = 'admin_api_experimental_endpoints';
    public const FEATURE_FLAG_FRONT_CONTAINER_V2 = 'front_container_v2';
    public const FEATURE_FLAG_IMPROVED_SHIPMENT = 'improved_shipment';
    public const FEATURE_FLAG_DISCOUNT = 'discount';
    public const FEATURE_FLAG_IMPROVED_B2B = 'improved_b2b';
    public const FEATURE_FLAG_NEW_PRICING = 'new_pricing';
    public const FEATURE_FLAG_EMAIL_BODY_TRANSLATION = 'email_body_translation';
    public const FEATURE_FLAG_HOOK_MODULE_V2 = 'hook_module_v2';
}
