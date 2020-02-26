<?php

namespace PrestaShopBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FileType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // makes it legal for FileType fields to have an image_property option
        $resolver->setDefined(['image_property']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['image_property'])) {
            $extraImagePath = $imageUrl = $form->getParent()->get('webPath');
            if ($extraImagePath) {
                $imageUrl = $form->getParent()->get('webPath')->getViewData();
                if (file_exists(_PS_ROOT_DIR_ . $imageUrl)) {
                    // sets an "image_url" variable that will be available when rendering this field
                    $view->vars['image_url'] = $imageUrl;
                }
            }
        }
    }
}
