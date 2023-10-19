<?php

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ApeType extends AbstractType implements DataTransformerInterface
{
    use TranslatorAwareTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                new Regex([
                    'pattern' => '/^[0-9]{1,2}?\.?[0-9]{1,2}[a-zA-Z]{1}$/s',
                    'message' => $this->trans('This field is invalid.', [], 'Admin.Notifications.Error'),
                ]),
            ],
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        return $value;
    }
}
