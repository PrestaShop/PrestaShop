<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionsColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'action';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'actions' => null,
                'filter_type' => SubmitType::class,
                'filter_type_options' => [],
            ])
            ->setAllowedTypes('filter_type', ['string', 'null'])
            ->setAllowedTypes('filter_type_options', 'array')
            ->setAllowedTypes('actions', ['null', RowActionCollectionInterface::class])
        ;
    }
}
