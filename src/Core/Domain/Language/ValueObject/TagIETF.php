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
