<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
