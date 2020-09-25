<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler;

/**
 * Stores results for handling forms.
 */
class FormHandlerResult implements FormHandlerResultInterface
{
    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var bool
     */
    private $isSubmitted;

    /**
     * @var int|null
     */
    private $identifiableObjectId;

    /**
     * @param int|null $identifiableObjectId ID of identifiable object or null if it does not exist
     * @param bool $isSubmitted
     * @param bool $isValid
     */
    private function __construct($identifiableObjectId, $isSubmitted, $isValid)
    {
        $this->identifiableObjectId = $identifiableObjectId;
        $this->isSubmitted = $isSubmitted;
        $this->isValid = $isValid;
    }

    /**
     * Creates successful form handler result with identifiable object id.
     *
     * @param int $identifiableObjectId
     *
     * @return FormHandlerResult
     */
    public static function createWithId($identifiableObjectId)
    {
        return new self(
            $identifiableObjectId,
            true,
            true
        );
    }

    /**
     * Creates form handler result when form which was provided form handling was not submitted
     *
     * @return FormHandlerResult
     */
    public static function createNotSubmitted()
    {
        return new self(
            null,
            false,
            false
        );
    }

    /**
     * Creates result for submitted but not valid form
     *
     * @return FormHandlerResult
     */
    public static function createSubmittedButNotValid()
    {
        return new self(
            null,
            true,
            false
        );
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * @return bool
     */
    public function isSubmitted()
    {
        return $this->isSubmitted;
    }

    /**
     * @return int|null
     */
    public function getIdentifiableObjectId()
    {
        return $this->identifiableObjectId;
    }
}
