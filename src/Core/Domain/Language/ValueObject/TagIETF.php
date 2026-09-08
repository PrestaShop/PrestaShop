<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;

/**
 * Stores IETF tag value (e.g. en-US)
 */
class TagIETF
{
    /**
     * Regexp to validate an IETF tag, the bounding anchors are not present, so you can choose them and it allows
     * this regexp to be used in routing configuration.
     */
    public const IETF_TAG_REGEXP = '^[a-zA-Z]{2}(-[a-zA-Z]{2})?$';

    /**
     * @var string
     */
    private $tagIETF;

    /**
     * @param string $tagIETF
     *
     * @throws LanguageConstraintException
     */
    public function __construct($tagIETF)
    {
        $this->assertIsTagIETF($tagIETF);

        $this->tagIETF = $tagIETF;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->tagIETF;
    }

    /**
     * The locale this tag stands for, in the `language-REGION` shape `Validate::isLocale()` accepts.
     *
     * A tag carrying a region gives it directly - `de-ch` is `de-CH`. A tag without one has no
     * region to offer, so the language is repeated as the region: `de` becomes `de-DE`. That is the
     * same shape PrestaShop's own list uses for such languages - `fr` is stored as `fr-FR` - and it
     * is a defined answer rather than a guess at the country the merchant meant.
     *
     * @return string
     */
    public function toLocale(): string
    {
        [$language, $region] = array_pad(explode('-', $this->tagIETF, 2), 2, null);

        return strtolower($language) . '-' . strtoupper($region ?? $language);
    }

    /**
     * @param string $tagIETF
     *
     * @throws LanguageConstraintException
     */
    private function assertIsTagIETF($tagIETF)
    {
        if (!is_string($tagIETF) || !preg_match(sprintf('/%s/', static::IETF_TAG_REGEXP), $tagIETF)) {
            throw new LanguageConstraintException(sprintf('Invalid IETF tag %s provided', var_export($tagIETF, true)), LanguageConstraintException::INVALID_IETF_TAG);
        }
    }
}
