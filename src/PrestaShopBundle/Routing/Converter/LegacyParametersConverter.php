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

namespace PrestaShopBundle\Routing\Converter;

/**
 * This class converts the request information (attributes and query parameters)
 * and returns an array of parameters adapted with their legacy names (based on
 * the configuration from the routing).
 */
class LegacyParametersConverter
{
    /**
     * Use the request attributes which contain the routing configuration along with query
     * parameters to return an array containing the equivalent with legacy parameters names.
     *
     * Example with $request being a Symfony Request:
     *
     * $legacyParameters = $converter->getParameters($request->attributes->all(), $request->query->all());
     *
     * @param array $requestAttributes
     * @param array $queryParameters
     *
     * @return array|null
     */
    public function getParameters(array $requestAttributes, array $queryParameters): ?array
    {
        if (!isset($requestAttributes['_legacy_parameters']) && !isset($requestAttributes['_legacy_link'])) {
            return null;
        }

        $legacyParameters = [];

        // Convert new parameters into legacy ones (search in attributes and query parameters)
        $parametersMatching = $requestAttributes['_legacy_parameters'] ?? [];
        foreach ($parametersMatching as $legacyParameter => $routingParameter) {
            if (isset($requestAttributes[$routingParameter])) {
                $legacyParameters[$legacyParameter] = $requestAttributes[$routingParameter];
            } elseif (isset($queryParameters[$routingParameter])) {
                $legacyParameters[$legacyParameter] = $queryParameters[$routingParameter];
            }
        }

        // Set controller and action based on _legacy_link
        if (isset($requestAttributes['_legacy_link'])) {
            $legacyLinks = $requestAttributes['_legacy_link'];
            if (!is_array($legacyLinks)) {
                $legacyLinks = [$legacyLinks];
            }

            // Loop through the _legacy_link until a controller and action is found
            foreach ($legacyLinks as $legacyLink) {
                $linkParts = explode(':', $legacyLink);
                if (!isset($legacyParameters['controller'])) {
                    $legacyParameters['controller'] = $linkParts[0];
                }
                if (!isset($legacyParameters['action']) && count($linkParts) > 1) {
                    $legacyParameters['action'] = $linkParts[1];
                }
                if (isset($legacyParameters['action'])) {
                    break;
                }
            }
        }

        return $legacyParameters;
    }
}
