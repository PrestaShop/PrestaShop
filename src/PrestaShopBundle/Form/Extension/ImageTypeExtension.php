<?php

namespace PrestaShopBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extends the FileType with the possibility show image if `webPath` property is added to the type
 */
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
        $resolver->setDefined(['delete_action_route']);
        $resolver->setDefined(['id_property']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['image_property'])) {
            $extraImagePath = $form->getParent()->get('webPath');
            if ($extraImagePath) {
                $imageUrl = $extraImagePath->getViewData();
                if (file_exists(_PS_ROOT_DIR_ . $imageUrl)) {
                    // sets an "image_url" variable that will be available when rendering this field
                    $view->vars['image_url'] = $imageUrl;
                }
            }
        }
        if (isset($options['delete_action_route'])) {
            $deleteActionRouteProperty = $form->getParent()->get('delete_action_route');
            if ($deleteActionRouteProperty) {
                $deleteActionRoute = $deleteActionRouteProperty->getViewData();
                $view->vars['delete_action_route'] = $deleteActionRoute;
            }
        }
        if (isset($options['id_property'])) {
            $idProperty = $form->getParent()->get('id');
            if ($idProperty) {
                $id = $idProperty->getViewData();
                $view->vars['id'] = $id;
            }
        }
    }
}
