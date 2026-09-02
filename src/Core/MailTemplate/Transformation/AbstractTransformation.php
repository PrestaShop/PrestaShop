<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate\Transformation;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;

/**
 * Class AbstractTransformation is a basic abstract class for TransformationInterface
 */
abstract class AbstractTransformation implements TransformationInterface
{
    /** @var LanguageInterface */
    protected $language;

    /** @var string */
    protected $type;

    /**
     * @param string $type
     *
     * @throws InvalidArgumentException
     */
    public function __construct($type)
    {
        $availableTypes = [
            MailTemplateInterface::HTML_TYPE,
            MailTemplateInterface::TXT_TYPE,
        ];
        if (!in_array($type, $availableTypes)) {
            throw new InvalidArgumentException(sprintf('Invalid type %s, available types are: %s', $type, implode(', ', $availableTypes)));
        }

        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setLanguage(LanguageInterface $language)
    {
        $this->language = $language;

        return $this;
    }
}
