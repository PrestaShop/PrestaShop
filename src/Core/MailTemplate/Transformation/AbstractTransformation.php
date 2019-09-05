<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
            throw new InvalidArgumentException(sprintf(
                'Invalid type %s, available types are: %s',
                $type,
                implode(', ', $availableTypes)
            ));
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
