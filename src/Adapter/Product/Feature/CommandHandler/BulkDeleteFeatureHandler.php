<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Product\Feature\CommandHandler;

use Feature;
use PrestaShop\PrestaShop\Adapter\Product\Feature\AbstractFeatureHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\Command\BulkDeleteFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\CommandHandler\BulkDeleteFeatureHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\Exception\CannotDeleteFeatureException;

/**
 * Handles BulkDeleteFeatureCommand using legacy object model
 */
final class BulkDeleteFeatureHandler extends AbstractFeatureHandler implements BulkDeleteFeatureHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteFeatureCommand $command): void
    {
        /** @var Feature $featureId */
        foreach ($command->getFeatureIds() as $featureId) {
            $feature = $this->getFeatureById($featureId);

            if (false === $this->deleteFeature($feature)) {
                throw new CannotDeleteFeatureException(sprintf(
                    'Cannot delete feature with id %s',
                    $feature->id
                ),
                    CannotDeleteFeatureException::FAILED_BULK_DELETE
                );
            }
        }
    }
}
