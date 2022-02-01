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
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * For combination update in bulk action
 */
class BulkCombinationType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $defaultCurrencyIsoCode;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     * @param string $defaultCurrencyIsoCode
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        string $defaultCurrencyIsoCode
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price_tax_excluded', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Impact on price (tax excl.)', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('Does this combination have a different price? Is it cheaper or more expensive than the default retail price?', 'Admin.Catalog.Help'),
                'attr' => ['data-display-price-precision' => self::PRESTASHOP_DECIMALS],
                'currency' => $this->defaultCurrencyIsoCode,
                'disabling_toggle' => true,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
            ])
            ->add('reference', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'disabling_toggle' => true,
                'required' => false,
                'label' => $this->trans('Reference', 'Admin.Global'),
                'label_help_box' => $this->trans('Your reference code for this product. Allowed special characters: .-_#.', 'Admin.Catalog.Help'),
            ])
        ;
    }
}
