<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Debug;

use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\Product\TrackedProductPrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\PriceBreakdown;

/**
 * Formats a TrackedProductPrice's PriceBreakdown into human-readable strings
 * or structured arrays suitable for Twig rendering in the debug toolbar.
 */
class ProductPriceHistoryDisplayer
{
    /**
     * Formats a ProductPrice's breakdown as a human-readable string.
     */
    public function formatAsString(ProductPriceInterface $productPrice): string
    {
        if (!$productPrice instanceof TrackedProductPrice) {
            return 'No tracking data available';
        }

        return $this->formatBreakdownAsString($productPrice->getBreakdown());
    }

    /**
     * Formats a ProductPrice's breakdown as a structured array for rendering.
     *
     * @return array<int, array{caller: string, line: int, property: string, previous: string, new: string}>
     */
    public function formatAsArray(ProductPriceInterface $productPrice): array
    {
        if (!$productPrice instanceof TrackedProductPrice) {
            return [];
        }

        return $this->formatBreakdownAsArray($productPrice->getBreakdown());
    }

    /**
     * @return string
     */
    public function formatBreakdownAsString(PriceBreakdown $breakdown): string
    {
        if ($breakdown->count() === 0) {
            return 'No modifications recorded';
        }

        $lines = [];
        foreach ($breakdown->getSteps() as $step) {
            $className = $this->getShortClassName($step->getCallerClass());
            $lines[] = sprintf(
                '[%s:%d] %s: %s -> %s',
                $className,
                $step->getCallerLine(),
                $step->getProperty(),
                $step->getPreviousValue(),
                $step->getNewValue()
            );
        }

        return implode("\n", $lines);
    }

    /**
     * @return array<int, array{caller: string, line: int, property: string, previous: string, new: string}>
     */
    public function formatBreakdownAsArray(PriceBreakdown $breakdown): array
    {
        $result = [];
        foreach ($breakdown->getSteps() as $step) {
            $result[] = [
                'caller' => $step->getCallerClass(),
                'line' => $step->getCallerLine(),
                'property' => $step->getProperty(),
                'previous' => $step->getPreviousValue(),
                'new' => $step->getNewValue(),
            ];
        }

        return $result;
    }

    protected function getShortClassName(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);

        return end($parts);
    }
}
