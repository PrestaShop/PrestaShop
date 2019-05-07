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

namespace PrestaShop\PrestaShop\Core\Hook\Generator;

use PrestaShop\PrestaShop\Core\Hook\HookDescription;

final class DynamicHookDescriptiveContentGenerator implements DynamicHookDescriptiveContentGeneratorInterface
{
    const STRING_STARTS_WITH_AND_ENDS_WITH_REGEX_PATTERN = '/%s(.+?)%s/ims';

    /**
     * @var array
     */
    private $hookDescriptions;

    /**
     * @param array $hookDescriptions
     */
    public function __construct(array $hookDescriptions)
    {
        $this->hookDescriptions = $hookDescriptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription($hookName)
    {
        foreach ($this->hookDescriptions as $hookPlaceholder => $hookDescription) {
            $prefix = isset($hookDescription['prefix']) ? $hookDescription['prefix'] : '';
            $suffix = isset($hookDescription['suffix']) ? $hookDescription['suffix'] : '';

            if ($this->doesHookSuffixAndPrefixMatches($hookName, $prefix, $suffix)) {
                return new HookDescription(
                    $hookName,
                    $hookDescription['title'],
                    $hookDescription['description']
                );
            }
        }

        return new HookDescription(
            $hookName,
            '',
            ''
        );
    }

    /**
     * Checks if hook starts with certain prefix and ends with certain suffix.
     *
     * @param string $hookName
     * @param string $prefix
     * @param string $suffix
     *
     * @return false|int
     */
    private function doesHookSuffixAndPrefixMatches($hookName, $prefix, $suffix)
    {
        $pattern = sprintf(
            self::STRING_STARTS_WITH_AND_ENDS_WITH_REGEX_PATTERN,
            preg_quote($prefix, '/'), preg_quote($suffix, '/')
        );

        return preg_match($pattern, $hookName);
    }
}
