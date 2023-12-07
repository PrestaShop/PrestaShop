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

namespace PrestaShopBundle\Form\Extension;

use PrestaShop\PrestaShop\Core\Localization\Number\LocaleNumberTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class NumberTypeExtension extends AbstractTypeExtension
{
    /**
     * @var LocaleNumberTransformer
     */
    private $localeNumberTransformer;

    public function __construct(
        LocaleNumberTransformer $localeNumberTransformer
    ) {
        $this->localeNumberTransformer = $localeNumberTransformer;
    }

    public static function getExtendedTypes(): iterable
    {
        return [NumberType::class];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // We only want to replace/adapt the NumberToLocalizedStringTransformer, so we save the current transformers
        // to restore them after replacing the new NumberToLocalizedStringTransformer.
        $viewTransformers = $builder->getViewTransformers();
        $builder->resetViewTransformers();

        foreach ($viewTransformers as $viewTransformer) {
            if ($viewTransformer instanceof NumberToLocalizedStringTransformer) {
                $builder
                    ->addViewTransformer(new NumberToLocalizedStringTransformer(
                        $options['scale'],
                        $options['grouping'],
                        $options['rounding_mode'],
                        $options['html5'] ? 'en' : $this->localeNumberTransformer->getLocaleForNumberInputs()
                    ))
                ;
            } else {
                $builder->addViewTransformer($viewTransformer);
            }
        }
    }
}
