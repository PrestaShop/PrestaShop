<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides context language iso code
 */
class ContextIsoCodeProviderExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $isoCode;

    /**
     * @param string $isoCode
     */
    public function __construct($isoCode)
    {
        $this->isoCode = $isoCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('get_context_iso_code', [$this, 'getIsoCode']),
        ];
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }
}
