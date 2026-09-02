<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\DataTransformer;

use PrestaShop\PrestaShop\Core\Util\InternationalizedDomainNameConverter;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class DefaultLanguageToFilledArrayDataTransformer is responsible for filling empty array values with
 * default language value if such exists.
 */
final class IDNConverterDataTransformer implements DataTransformerInterface
{
    /**
     * @var InternationalizedDomainNameConverter
     */
    private $converter;

    public function __construct(InternationalizedDomainNameConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     *
     * Do not convert utf8 to punycode, should be done on the client side
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * Convert punycode to utf8 (prestashop@xn--80aswg.xn--p1ai -> prestashop@сайт.рф)
     */
    public function reverseTransform($value)
    {
        return is_string($value) ? $this->converter->emailToUtf8($value) : $value;
    }
}
