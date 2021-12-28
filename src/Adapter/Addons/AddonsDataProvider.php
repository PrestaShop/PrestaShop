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

namespace PrestaShop\PrestaShop\Adapter\Addons;

use Exception;
use PhpEncryption;
use PrestaShop\PrestaShop\Adapter\Module\ModuleZipManager;
use PrestaShopBundle\Service\DataProvider\Admin\AddonsInterface;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data provider for new Architecture, about Addons.
 *
 * This class will provide data from Addons API
 */
class AddonsDataProvider implements AddonsInterface
{
    /** @var string */
    public const ADDONS_API_MODULE_CHANNEL_STABLE = 'stable';

    /** @var string */
    public const ADDONS_API_MODULE_CHANNEL_BETA = 'beta';

    /** @var string */
    public const ADDONS_API_MODULE_CHANNEL_ALPHA = 'alpha';

    /** @var array<string> */
    public const ADDONS_API_MODULE_CHANNELS = [
        self::ADDONS_API_MODULE_CHANNEL_STABLE,
        self::ADDONS_API_MODULE_CHANNEL_BETA,
        self::ADDONS_API_MODULE_CHANNEL_ALPHA,
    ];

    /**
     * @var bool
     */
    protected static $is_addons_up = true;

    /**
     * @var ApiClient
     */
    private $marketplaceClient;

    /**
     * @var ModuleZipManager
     */
    private $zipManager;

    /**
     * @var PhpEncryption
     */
    private $encryption;

    /**
     * @var string the cache directory location
     */
    public $cacheDir;

    /**
     * @var string
     */
    private $moduleChannel;

    /**
     * @param ApiClient $apiClient
     * @param ModuleZipManager $zipManager
     * @param string|null $moduleChannel
     */
    public function __construct(
        ApiClient $apiClient,
        ModuleZipManager $zipManager,
        ?string $moduleChannel = null
    ) {
        $this->marketplaceClient = $apiClient;
        $this->zipManager = $zipManager;
        $this->encryption = new PhpEncryption(_NEW_COOKIE_KEY_);
        $this->moduleChannel = $moduleChannel ?? self::ADDONS_API_MODULE_CHANNEL_STABLE;
    }

    /**
     * {@inheritdoc}
     */
    public function request($action, $params = [])
    {
        if (!$this->isAddonsUp()) {
            throw new Exception('Previous call failed and disabled client.');
        }

        $this->marketplaceClient->reset();

        try {
            switch ($action) {
                case 'categories':
                    return $this->marketplaceClient->getCategories();
            }
        } catch (Exception $e) {
            self::$is_addons_up = false;

            throw $e;
        }
    }

    /**
     * Check if a request has already failed.
     *
     * @return bool
     */
    public function isAddonsUp(): bool
    {
        return self::$is_addons_up;
    }
}
