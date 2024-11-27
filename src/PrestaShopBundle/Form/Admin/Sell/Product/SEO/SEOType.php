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

namespace PrestaShopBundle\Form\Admin\Sell\Product\SEO;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShopBundle\Form\Admin\Type\TextWithLengthCounterType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class SEOType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var bool
     */
    private $friendlyUrlEnabled;

    /**
     * @var bool
     */
    private $forceFriendlyUrl;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     * @param bool $friendlyUrlEnabled
     * @param bool $forceFriendlyUrl
     * @param LegacyContext $legacyContext
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        bool $friendlyUrlEnabled,
        bool $forceFriendlyUrl,
        LegacyContext $legacyContext,
        ConfigurationInterface $configuration
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->friendlyUrlEnabled = $friendlyUrlEnabled;
        $this->forceFriendlyUrl = $forceFriendlyUrl;
        $this->legacyContext = $legacyContext;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('meta_information', MetaInformationType::class)
            ->add('redirect_option', RedirectOptionType::class, [
                'product_id' => $options['product_id'],
            ])
            ->add('tags', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Tags', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'label_subtitle' => $this->trans('Enter the keywords that customers might search for when looking for this product.', 'Admin.Catalog.Feature'),
                'help' => sprintf(
                    '%s %s',
                    $this->trans('Separate each tag with a comma or press the Enter key.', 'Admin.Catalog.Help'),
                    $this->trans('Invalid characters: %s', 'Admin.Notifications.Info', [TypedRegexValidator::GENERIC_NAME_CHARS])
                ),
                'external_link' => [
                    'href' => $this->legacyContext->getAdminLink('AdminTags', true),
                    'text' => $this->trans('[1]Manage all tags[/1]', 'Admin.Catalog.Feature'),
                ],
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                    ],
                    'attr' => [
                        'class' => 'js-taggable-field',
                    ],
                    'required' => false,
                ],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => $this->trans('SEO', 'Admin.Catalog.Feature'),
                'active' => false,
            ])
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setAllowedTypes('active', ['bool'])
        ;
    }
}
