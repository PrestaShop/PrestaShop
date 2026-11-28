<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Seo;

/**
 * Tells if a URL must not be indexed by search engines because of the parameters of its query string.
 */
final class NoIndexUrlParametersMatcher
{
    public const PARAMETERS_SEPARATOR = ',';

    /**
     * @var string[]
     */
    private readonly array $parameterNames;

    public function __construct(string $parameterNames)
    {
        $this->parameterNames = self::parseParameterNames($parameterNames);
    }

    public function matchesUrl(string $url): bool
    {
        if ([] === $this->parameterNames) {
            return false;
        }

        $queryString = parse_url($url, PHP_URL_QUERY);
        if (!is_string($queryString)) {
            return false;
        }

        parse_str($queryString, $queryParameters);

        return [] !== array_intersect($this->parameterNames, array_keys($queryParameters));
    }

    /**
     * @return string[]
     */
    public static function parseParameterNames(string $parameterNames): array
    {
        $names = array_map('trim', explode(self::PARAMETERS_SEPARATOR, $parameterNames));

        return array_values(array_unique(array_filter($names, static fn (string $name): bool => '' !== $name)));
    }
}
