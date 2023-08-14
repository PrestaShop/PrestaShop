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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Search;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AliasType
 */
class AliasType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $employeeIsoCode;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        string $employeeIsoCode
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->employeeIsoCode = $employeeIsoCode;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('aliases', TranslatableType::class, [
                'required' => true,
                'label' => $this->trans('Aliases', 'Admin.Shopparameters.Help'),
                'help' => sprintf(
                    '%s',
                    $this->trans('Enter each alias separated by a comma (e.g. \'prestshop,preztashop,prestasohp\'). Invalid characters: %s', 'Admin.Notifications.Info', [TypedRegexValidator::GENERIC_NAME_CHARS])
                ),
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
            ->add('search_terms', EntitySearchInputType::class, [
                'label' => $this->trans('Search Terms', 'Admin.Shopparameters.Help'),
                'remote_url' => $this->router->generate('admin_search_index', [
                    'languageCode' => $this->employeeIsoCode,
                    'query' => '__QUERY__',
                ]),
                'min_length' => 3,
                'placeholder' => $this->trans('Search term', 'Admin.Catalog.Help'),
            ])
        ;
    }
}
