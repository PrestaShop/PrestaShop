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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\EditCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CarrierFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function create(array $data)
    {
        /** @var UploadedFile|null $logo */
        $logo = $data['general_settings']['logo'];
        if ($logo instanceof UploadedFile) {
            $logoPath = $logo->getPathname();
        } else {
            $logoPath = null;
        }

        /** @var CarrierId $carrierId */
        $carrierId = $this->commandBus->handle(new AddCarrierCommand(
            $data['general_settings']['name'],
            $data['general_settings']['localized_delay'],
            $data['general_settings']['grade'],
            $data['general_settings']['tracking_url'] ?? '',
            0, // @todo: should not be in the add command but auto-computed or at least be optional
            (bool) $data['general_settings']['active'],
            $data['general_settings']['group_access'],
            (bool) $data['shipping_settings']['has_additional_handling_fee'],
            (bool) $data['shipping_settings']['is_free'],
            $data['shipping_settings']['shipping_method'],
            $data['shipping_settings']['id_tax_rule_group'],
            $data['shipping_settings']['range_behavior'],
            $data['size_weight_settings']['max_width'] ?? 0,
            $data['size_weight_settings']['max_height'] ?? 0,
            $data['size_weight_settings']['max_depth'] ?? 0,
            $data['size_weight_settings']['max_weight'] ?? 0,
            $logoPath,
        ));

        return $carrierId->getValue();
    }

    public function update($id, array $data)
    {
        $command = new EditCarrierCommand($id);
        $command
            ->setName($data['general_settings']['name'])
            ->setLocalizedDelay($data['general_settings']['localized_delay'])
            ->setGrade($data['general_settings']['grade'])
            ->setActive((bool) $data['general_settings']['active'])
            ->setTrackingUrl($data['general_settings']['tracking_url'] ?? '')
            ->setMaxWidth($data['size_weight_settings']['max_width'] ?? null)
            ->setMaxHeight($data['size_weight_settings']['max_height'] ?? null)
            ->setMaxDepth($data['size_weight_settings']['max_depth'] ?? null)
            ->setMaxWeight($data['size_weight_settings']['max_weight'] ?? null)
            ->setAssociatedGroupIds($data['general_settings']['group_access'] ?? null)
        ;
        /** @var UploadedFile|null $logo */
        $logo = $data['general_settings']['logo'];
        if ($logo instanceof UploadedFile) {
            $command->setLogoPathName($logo->getPathname());
        }

        /** @var CarrierId $newCarrierId */
        $newCarrierId = $this->commandBus->handle($command);

        return $newCarrierId->getValue();
    }
}
