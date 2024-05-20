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

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Carrier;
use Configuration;
use Context;
use Exception;
use Group;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\EditCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\EditableCarrier;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShopException;
use Tests\Resources\DummyFileUploader;
use Tests\Resources\Resetter\CarrierResetter;

class CarrierFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @AfterSuite
     */
    public static function restoreCarrierTablesAfterSuite(): void
    {
        CarrierResetter::resetCarrier();
    }

    /**
     * @todo: It is a temporary method to use sharedStorage and should be improved once Carrier creation is migrated.
     *
     * @Given carrier :carrierReference named :carrierName exists
     *
     * @param string $carrierReference
     * @param string $carrierName
     *
     * @throws PrestaShopException
     */
    public function createDefaultIfNotExists(string $carrierReference, string $carrierName): void
    {
        if ($this->getSharedStorage()->exists($carrierReference)) {
            return;
        }

        $carrier = new Carrier(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $carrier->name = $carrierName;
        $carrier->shipping_method = Carrier::SHIPPING_METHOD_PRICE;
        $carrier->delay = '28 days later';
        $carrier->active = true;
        $carrier->add();

        $groups = Group::getGroups(Context::getContext()->language->id);
        $groupIds = [];
        foreach ($groups as $group) {
            $groupIds[] = $group['id_group'];
        }

        $carrier->setGroups($groupIds);

        $this->getSharedStorage()->set($carrierReference, (int) $carrier->id);
    }

    /**
     * @When I create carrier :reference with specified properties:
     */
    public function createCarrier(string $reference, TableNode $node): void
    {
        $properties = $this->localizeByRows($node);
        try {
            if (isset($properties['logoPathName']) && 'null' !== $properties['logoPathName']) {
                $tmpLogo = DummyFileUploader::upload($properties['logoPathName']);
                $properties['logoPathName'] = DummyFileUploader::upload($properties['logoPathName']);
            }

            $carrierId = $this->createCarrierUsingCommand(
                $properties['name'],
                $properties['delay'],
                (int) $properties['grade'],
                $properties['trackingUrl'],
                (int) $properties['position'],
                (bool) $properties['active'],
                $properties['logoPathName'] ?? null
            );

            if (isset($tmpLogo)) {
                $this->fakeUploadLogo($tmpLogo, $carrierId->getValue());
            }

            $this->getSharedStorage()->set($reference, $carrierId->getValue());
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit carrier :reference called :newReference with specified properties:
     */
    public function editCarrier(string $reference, string $newReference, TableNode $node): void
    {
        $properties = $this->localizeByRows($node);
        $carrierId = $this->referenceToId($reference);

        try {
            $command = new EditCarrierCommand($carrierId);

            if (isset($properties['name'])) {
                $command->setName($properties['name']);
            }
            if (isset($properties['delay'])) {
                $command->setLocalizedDelay($properties['delay']);
            }
            if (isset($properties['grade'])) {
                $command->setGrade((int) $properties['grade']);
            }
            if (isset($properties['trackingUrl'])) {
                $command->setTrackingUrl($properties['trackingUrl']);
            }
            if (isset($properties['position'])) {
                $command->setPosition((int) $properties['position']);
            }
            if (isset($properties['active'])) {
                $command->setActive((bool) $properties['active']);
            }

            if (isset($properties['logoPathName']) && 'null' !== $properties['logoPathName']) {
                if ('' !== $properties['logoPathName']) {
                    $tmpLogo = DummyFileUploader::upload($properties['logoPathName']);
                }
                $command->setLogoPathName($tmpLogo ?? '');
            }

            $newCarrierId = $this->getCommandBus()->handle($command);

            if (isset($tmpLogo)) {
                $this->fakeUploadLogo($tmpLogo, $newCarrierId->getValue());
            }
            $this->getSharedStorage()->set($newReference, $newCarrierId->getValue());
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then carrier :reference should have the following properties:
     *
     * @param string $reference
     * @param TableNode $tableNode
     */
    public function assertCarrierProperties(string $reference, TableNode $tableNode): void
    {
        $carrier = $this->getCarrier($reference);
        $data = $this->localizeByRows($tableNode);
        if (isset($data['name'])) {
            Assert::assertEquals($data['name'], $carrier->getName());
        }
        if (isset($data['grade'])) {
            Assert::assertEquals($data['grade'], $carrier->getGrade());
        }
        if (isset($data['trackingUrl'])) {
            Assert::assertEquals($data['trackingUrl'], $carrier->getTrackingUrl());
        }
        if (isset($data['position'])) {
            Assert::assertEquals($data['position'], $carrier->getPosition());
        }
        if (isset($data['active'])) {
            Assert::assertEquals($data['active'], $carrier->isActive());
        }
        if (isset($data['delay'])) {
            Assert::assertEquals($data['delay'], $carrier->getLocalizedDelay());
        }
    }

    /**
     * @Then carrier :reference should have a logo
     */
    public function carrierShouldHaveALogo(string $reference)
    {
        $carrier = $this->getCarrier($reference);
        Assert::assertEquals($carrier->getLogoPath(), _THEME_SHIP_DIR_ . $carrier->getCarrierId() . '.jpg');
    }

    /**
     * @Then carrier :reference shouldn't have a logo
     */
    public function carrierShouldntHaveALogo(string $reference)
    {
        $carrier = $this->getCarrier($reference);
        Assert::assertNull($carrier->getLogoPath());
    }

    private function createCarrierUsingCommand(
        string $name,
        array $delay,
        int $grade,
        string $trackingUrl,
        int $position,
        bool $active,
        ?string $logoPathName
    ): CarrierId {
        $command = new AddCarrierCommand(
            $name,
            $delay,
            $grade,
            $trackingUrl,
            $position,
            $active,
            $logoPathName
        );

        return $this->getCommandBus()->handle($command);
    }

    private function getCarrier(string $reference): EditableCarrier
    {
        $id = $this->referenceToId($reference);

        return $this->getCommandBus()->handle(new GetCarrierForEditing($id));
    }

    private function fakeUploadLogo(string $filename, int $carrierId): void
    {
        if ('' !== $filename) {
            copy($filename, _PS_SHIP_IMG_DIR_ . $carrierId . '.jpg');
        }
    }
}
