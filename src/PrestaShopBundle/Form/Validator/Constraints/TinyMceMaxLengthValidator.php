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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Validator\Constraints;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Adapter\Validate;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    /**
     * @var Validate
     */
    private $validateAdapter;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(Validate $validate, TranslatorInterface $translator)
    {
        $this->validateAdapter = $validate;
        $this->translator = $translator;
    }

    /**
     * @param mixed $value
     * @param TinyMceMaxLength $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TinyMceMaxLength) {
            throw new UnexpectedTypeException($constraint, TinyMceMaxLength::class);
        }

        if (!$this->validateAdapter->isUnsignedInt($constraint->max)) {
            throw new InvalidArgumentException('Max must be int. Input was: ' . \gettype($constraint->max));
        }

        $replaceArray = [
            "\n",
            "\r",
            "\n\r",
            "\r\n",
        ];
        $str = str_replace($replaceArray, [''], strip_tags($value));

        if (iconv_strlen($str) > $constraint->max) {
            $message = $constraint->message ?? $this->translator->trans(
                'This value is too long. It should have %limit% characters or less.',
                ['%limit%' => $constraint->max],
                'Admin.Catalog.Notification'
            );

            $this->context->buildViolation($message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(TinyMceMaxLength::TOO_LONG_ERROR_CODE)
                ->addViolation()
            ;
        }
    }
}
