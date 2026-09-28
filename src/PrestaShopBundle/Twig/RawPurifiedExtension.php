<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig;

use PrestaShopBundle\Utils\HTMLPurifier;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class RawPurifiedExtension extends AbstractExtension
{
    public function __construct(
        private readonly HTMLPurifier $htmlPurifier
    ) {
    }

    /**
     * Defines available filters.
     *
     * @return array Twig_SimpleFilter
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('raw_purified', [$this, 'rawPurifier'], ['is_safe' => ['all']]),
        ];
    }

    public function rawPurifier(?string $toPurify): string
    {
        // Twig resolves an undefined or null variable to null, and a filter that renders markup has
        // no reason to fail on that - it has nothing to purify and nothing to print.
        if (null === $toPurify) {
            return '';
        }

        return $this->htmlPurifier->purify($toPurify);
    }

    public function getName(): string
    {
        return 'twig_raw_purified_extension';
    }
}
