<?php

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\AbstractBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * initialises async process of submitting data to server - this helps to divide request to server. Useful for large
 * operations.
 */
final class SubmitAsyncBulkAction extends AbstractBulkAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'submit_async';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'submit_route',
            ])
            ->setDefaults([
                'confirm_message' => null,
                'submit_method' => 'POST',
                'modal_options' => null,
                'route_params' => [],
                'chunk_size' => 1,
            ])
            ->setAllowedTypes('submit_route', 'string')
            ->setAllowedTypes('confirm_message', ['string', 'null'])
            ->setAllowedValues('submit_method', ['POST', 'GET', 'DELETE', 'PUT'])
            ->setAllowedTypes('modal_options', [ModalOptions::class, 'null'])
            ->setAllowedTypes('route_params', 'array')
            ->setAllowedTypes('step', 'int')
        ;

        $resolver->setAllowedValues('chunk_size', static function ($value) {
            return $value >= 1;
        });
    }
}
