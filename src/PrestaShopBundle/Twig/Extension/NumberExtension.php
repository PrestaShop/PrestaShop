<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Extension;

use PrestaShop\Decimal\DecimalNumber;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class NumberExtension provides helper function to create Prestashop/Decimal in twig
 */
class NumberExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [new TwigFunction('number', [$this, 'createNumber'])];
    }

    public function createNumber($number)
    {
        return new DecimalNumber((string) $number);
    }
}
