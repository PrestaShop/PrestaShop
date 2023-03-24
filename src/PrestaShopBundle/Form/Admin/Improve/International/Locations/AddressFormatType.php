<?php

namespace PrestaShopBundle\Form\Admin\Improve\International\Locations;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressFormat;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetAddressFormatData;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\AddressFormatData;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddressFormatType extends TranslatorAwareType
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        CommandBusInterface $queryBus
    ) {
        parent::__construct($translator, $locales);
        $this->queryBus = $queryBus;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address_format', TextareaType::class, [
                'label' => $this->trans('Address format', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new AddressFormat(),
                ],
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['available_fields'] = $options['available_fields'];
        $view->vars['address_format'] = $options['address_format'];
        $view->vars['default_format'] = $options['default_format'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        /** @var AddressFormatData $addressFormat */
        $addressFormat = $this->queryBus->handle(new GetAddressFormatData);

        parent::configureOptions($resolver);
        $resolver
            ->setRequired([
                'available_fields',
                'address_format',
                'default_format',
            ])
            ->setAllowedTypes('available_fields', 'array')
            ->setAllowedTypes('address_format', 'string')
            ->setAllowedTypes('default_format', 'string')
            ->setDefaults([
                'form_theme' => '@PrestaShop/Admin/Improve/International/Country/FormTheme/country.html.twig',
                'available_fields' => $addressFormat->getAvailableFields(),
                'address_format' => urlencode($addressFormat->getAddressFormat()),
                'default_format' => urlencode($addressFormat->getDefaultFormat()),
            ])
        ;
    }
}
