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

namespace PrestaShopBundle\Form\Validator\Constraints;

use PrestaShopBundle\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * The computation here means to only count the raw text, not the rich text with html strip tags, also all the
 * line breaks are simply ignored (not event replaced with spaces). This computation is made to match the one
 * from the TinyMce text count. You can see it in TinyMCEEditor.js component, if the js component is modified
 * so should this validator.
 *
 * Note: if you rely on Product class validation you might also need to update Product::validateField
 * Note: if you are still using the legacy AdminProductsController you should also update the checkProduct() function
 */
class TinyMceMaxLengthValidator extends ConstraintValidator
{
    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param mixed $value
     * @param TinyMceMaxLength $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $replaceArray = [
            "\n",
            "\r",
            "\n\r",
            "\r\n",
        ];
        $str = str_replace($replaceArray, [''], strip_tags($value));
        $length = iconv_strlen($str);

        if ($length > $constraint->max) {
            $this->context->addViolation(
                $this->translator->trans('This value is too long. It should have %limit% characters or less.', [], 'Admin.Catalog.Notification'),
                ['%limit%' => $constraint->max]
            );
        }
    }
}
