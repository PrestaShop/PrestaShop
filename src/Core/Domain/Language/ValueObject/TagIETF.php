<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Language\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;

/**
 * Stores IETF tag value (e.g. en-US)
 */
class TagIETF
{
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
        if (!is_string($tagIETF) || !preg_match('/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/', $tagIETF)) {
            throw new LanguageConstraintException(sprintf('Invalid IETF tag %s provided', var_export($tagIETF, true)), LanguageConstraintException::INVALID_IETF_TAG);
        }
    }
}
