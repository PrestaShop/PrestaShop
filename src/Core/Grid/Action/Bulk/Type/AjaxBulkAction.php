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

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\AbstractBulkAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BulkAction holds data about single bulk action available in grid.
 */
final class AjaxBulkAction extends AbstractBulkAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'ajax';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'attributes' => [],
                'class' => '',
                // Request params
                'ajax_route' => '',
                'route_params' => [],
                'request_param_name' => 'bulk_ids',
                'bulk_chunk_size' => 10,
                'reload_after_bulk' => true,
                // Modal params
                'confirm_bulk_action' => true,
                'modal_confirm_title' => 'Apply bulk modifications',
                'modal_cancel' => 'Cancel',
                'modal_progress_title' => 'Completing %total% actions',
                'modal_progress_message' => 'Processing %done% / %total% elements.',
                'modal_close' => 'Close',
                'modal_stop_processing' => 'Stop processing',
                'modal_errors_message' => '%error_count% errors occurred. You can download the logs for future reference.',
                'modal_back_to_processing' => 'Back to processing',
                'modal_download_error_log' => 'Download error log',
                'modal_view_error_log' => 'View %error_count% error logs',
                'modal_error_title' => 'Error log',
            ])
            ->setAllowedTypes('class', 'string')
            ->setAllowedTypes('attributes', 'array')
            // Request params
            ->setAllowedTypes('ajax_route', 'string')
            ->setAllowedTypes('route_params', 'array')
            ->setAllowedTypes('request_param_name', 'string')
            ->setAllowedTypes('bulk_chunk_size', 'int')
            ->setAllowedTypes('reload_after_bulk', 'bool')
            // Modal params
            ->setAllowedTypes('confirm_bulk_action', 'bool')
            ->setAllowedTypes('modal_confirm_title', 'string')
            ->setAllowedTypes('modal_cancel', 'string')
            ->setAllowedTypes('modal_progress_title', 'string')
            ->setAllowedTypes('modal_progress_message', 'string')
            ->setAllowedTypes('modal_close', 'string')
            ->setAllowedTypes('modal_stop_processing', 'string')
            ->setAllowedTypes('modal_errors_message', 'string')
            ->setAllowedTypes('modal_back_to_processing', 'string')
            ->setAllowedTypes('modal_download_error_log', 'string')
            ->setAllowedTypes('modal_view_error_log', 'string')
            ->setAllowedTypes('modal_error_title', 'string')
        ;
    }
}
