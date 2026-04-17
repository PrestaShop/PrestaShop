<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
