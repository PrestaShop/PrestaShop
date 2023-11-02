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

namespace PrestaShop\PrestaShop\Adapter\Smarty;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class will manage Smarty configuration for a Shop.
 */
class SmartyCacheConfiguration implements DataConfigurationInterface
{
    private const DEFINES_FILE = _PS_ROOT_DIR_ . '/config/defines.inc.php';
    private const CUSTOM_DEFINES_FILE = _PS_ROOT_DIR_ . '/config/defines_custom.inc.php';
    private const PATTERN = '/(define\(\'_PS_SMARTY_CACHING_TYPE_\', \')([a-zA-Z]+)(\'\);)/Ui';

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'template_compilation' => $this->configuration->get('PS_SMARTY_FORCE_COMPILE'),
            'cache' => $this->configuration->getBoolean('PS_SMARTY_CACHE'),
            'multi_front_optimization' => $this->configuration->getBoolean('PS_SMARTY_LOCAL'),
            'caching_type' => $this->getSmartyCachingType(),
            'clear_cache' => $this->configuration->get('PS_SMARTY_CLEAR_CACHE'),
            'smarty_console' => $this->configuration->get('PS_SMARTY_CONSOLE'),
            'smarty_console_key' => $this->configuration->get('PS_SMARTY_CONSOLE_KEY'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Note: 'smarty_console' and 'smarty_console_key' keys are not allowed for update.
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_SMARTY_FORCE_COMPILE', $configuration['template_compilation']);
            $this->configuration->set('PS_SMARTY_CACHE', $configuration['cache']);
            $this->configuration->set('PS_SMARTY_LOCAL', $configuration['multi_front_optimization']);
            $this->configuration->set('PS_SMARTY_CLEAR_CACHE', $configuration['clear_cache']);
            if (!$this->setSmartyCachingType($configuration['caching_type'])) {
                $errors[] = [
                    'key' => 'Error: Could not write to file. Make sure that the correct permissions are set on the file %s',
                    'domain' => 'Admin.Advparameters.Notification',
                    'parameters' => [self::DEFINES_FILE],
                ];
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['template_compilation'],
            $configuration['cache'],
            $configuration['multi_front_optimization'],
            $configuration['clear_cache'],
            $configuration['smarty_console'],
            $configuration['smarty_console_key']
        );
    }

    private function getSmartyCachingType(): string
    {
        return defined('_PS_SMARTY_CACHING_TYPE_')
            ? _PS_SMARTY_CACHING_TYPE_
            : 'filesystem'
        ;
    }

    private function setSmartyCachingType(string $cachingType): bool
    {
        $replacement = '$1' . $cachingType . '$3';

        $cleanedContent = false;
        $file = self::CUSTOM_DEFINES_FILE;
        $content = '';

        if (is_readable(self::CUSTOM_DEFINES_FILE)) {
            $content = file_get_contents(self::CUSTOM_DEFINES_FILE);
            $cleanedContent = php_strip_whitespace(self::CUSTOM_DEFINES_FILE);
        }

        if (!$cleanedContent || !preg_match(self::PATTERN, $cleanedContent)) {
            $content = file_get_contents(self::DEFINES_FILE);
            $cleanedContent = php_strip_whitespace(self::DEFINES_FILE);
            $file = self::DEFINES_FILE;
            if (!$cleanedContent || !preg_match(self::PATTERN, $cleanedContent)) {
                return false;
            }
        }

        $status = file_put_contents($file, preg_replace(self::PATTERN, $replacement, $content));

        if (function_exists('opcache_invalidate')) {
           @opcache_invalidate($file);
        }

        return $status !== false;
    }
}
