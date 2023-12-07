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

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Localization\Currency\PatternTransformer;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Number\LocaleNumberTransformer;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price;
use PrestaShopBundle\Form\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer as SymfonyMoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

// TODO: in 9.0, we need to migrate this class into PrestaShopBundle/Form/Extension/MoneyTypeExtension
class CustomMoneyType extends AbstractTypeExtension
{
    public const PRESTASHOP_DECIMALS = 6;

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * @var LocaleNumberTransformer
     */
    private $localeNumberTransformer;

    /**
     * @param Locale $locale
     * @param int $defaultCurrencyId
     * @param CurrencyRepository $currencyRepository
     */
    public function __construct(
        Locale $locale,
        int $defaultCurrencyId,
        CurrencyRepository $currencyRepository,
        LocaleNumberTransformer $localeNumberTransformer
    ) {
        $this->locale = $locale;
        $this->defaultCurrencyId = $defaultCurrencyId;
        $this->currencyRepository = $currencyRepository;
        $this->localeNumberTransformer = $localeNumberTransformer;
    }

    public static function getExtendedTypes(): iterable
    {
        return [MoneyType::class];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'precision' => null,
            'scale' => self::PRESTASHOP_DECIMALS,
            'grouping' => false,
            'divisor' => 1,
            'currency' => $this->currencyRepository->getIsoCode(new CurrencyId($this->defaultCurrencyId)) ?: 'EUR',
            'compound' => false,
        ]);

        $resolver->setAllowedTypes('scale', 'int');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // We only want to replace/adapt the MoneyToLocalizedStringTransformer, so we save the current transformers
        // to restore them after replacing the new MoneyToLocalizedStringTransformer.
        $viewTransformers = $builder->getViewTransformers();
        $builder->resetViewTransformers();

        foreach ($viewTransformers as $viewTransformer) {
            if ($viewTransformer instanceof MoneyToLocalizedStringTransformer || $viewTransformer instanceof SymfonyMoneyToLocalizedStringTransformer) {
                $builder
                    ->addViewTransformer(new MoneyToLocalizedStringTransformer(
                        $options['scale'],
                        $options['grouping'],
                        $options['rounding_mode'],
                        $options['divisor'],
                        $this->localeNumberTransformer->getLocaleForNumberInputs()
                    ))
                ;
            } else {
                $builder->addViewTransformer($viewTransformer);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['money_pattern'] = $this->getFrameworkPattern($options['currency']);
    }

    /**
     * Provides currency pattern understandable to symfony, but uses prestashop Locale.
     *
     * @param string $currencyCode e.g. EUR, USD
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws LocalizationException
     */
    private function getFrameworkPattern(string $currencyCode): string
    {
        $priceSpecification = $this->locale->getPriceSpecification($currencyCode);

        if (!($priceSpecification instanceof Price)) {
            throw new InvalidArgumentException(sprintf('Expected instance of %s', Price::class));
        }

        // replace CLDR pattern placeholder with one understandable for framework
        $frameworkPattern = str_replace(
            Price::PATTERN_BASE_PLACEHOLDER,
            '{{ widget }}',
            $priceSpecification->getPositivePattern()
        );

        // insert currency symbol from CLDR instead of symbol placeholder
        return str_replace(
            PatternTransformer::CURRENCY_SYMBOL,
            $priceSpecification->getCurrencySymbol(),
            $frameworkPattern
        );
    }
}
