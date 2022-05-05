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
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
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
                'ajax_route' => '',
                'class' => '',
                'modal_title' => 'Completing action',
                'modal_close' => 'Close',
                'modal_stop_processing' => 'Stop processing',
                'modal_errors_occurred' => '%d errors occurred. You can download the logs for future reference.',
                'modal_back_to_processing' => 'Back to processing',
                'modal_download_error_log' => 'Download error log',
                'modal_view_error_log' => 'View %d error logs',
                'modal_error_log_title' => 'Error log',
                'route_params' => [],
            ])
            ->setAllowedTypes('class', 'string')
            ->setAllowedTypes('modal_title', 'string')
            ->setAllowedTypes('modal_close', 'string')
            ->setAllowedTypes('modal_stop_processing', 'string')
            ->setAllowedTypes('modal_errors_occurred', 'string')
            ->setAllowedTypes('modal_back_to_processing', 'string')
            ->setAllowedTypes('modal_download_error_log', 'string')
            ->setAllowedTypes('modal_view_error_log', 'string')
            ->setAllowedTypes('modal_error_log_title', 'string')
            ->setAllowedTypes('ajax_route', 'string')
            ->setAllowedTypes('attributes', 'array')
            ->setAllowedTypes('attributes', 'array');
    }
}
