<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Extension;

use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds color calculation functions to Twig.
 */
class ColorBrightnessCalculatorExtension extends AbstractExtension
{
    /**
     * @var ColorBrightnessCalculator
     */
    private $brightnessCalculator;

    /**
     * @param ColorBrightnessCalculator $brightnessCalculator
     */
    public function __construct(ColorBrightnessCalculator $brightnessCalculator)
    {
        $this->brightnessCalculator = $brightnessCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'is_color_bright',
                [$this->brightnessCalculator, 'isBright']
            ),
        ];
    }
}
