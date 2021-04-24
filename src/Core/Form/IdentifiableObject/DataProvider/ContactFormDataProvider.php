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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult\EditableContact;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ContactFormDataProvider is responsible for providing form data for contacts by contact id or by giving default
 * values.
 */
final class ContactFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @var DataTransformerInterface
     */
    private $stringArrayToIntegerArrayDataTransformer;

    /**
     * @param CommandBusInterface $queryBus
     * @param DataTransformerInterface $stringArrayToIntegerArrayDataTransformer
     * @param int[] $contextShopIds
     */
    public function __construct(
        CommandBusInterface $queryBus,
        DataTransformerInterface $stringArrayToIntegerArrayDataTransformer,
        array $contextShopIds
    ) {
        $this->queryBus = $queryBus;
        $this->contextShopIds = $contextShopIds;
        $this->stringArrayToIntegerArrayDataTransformer = $stringArrayToIntegerArrayDataTransformer;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ContactException
     */
    public function getData($contactId)
    {
        /** @var EditableContact $editableContact */
        $editableContact = $this->queryBus->handle(new GetContactForEditing($contactId));

        return [
            'title' => $editableContact->getLocalisedTitles(),
            'email' => null !== $editableContact->getEmail() ? $editableContact->getEmail()->getValue() : '',
            'is_messages_saving_enabled' => $editableContact->isMessagesSavingEnabled(),
            'description' => $editableContact->getLocalisedDescription(),
            'shop_association' => $editableContact->getShopAssociation(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $shopIds = $this->stringArrayToIntegerArrayDataTransformer->reverseTransform($this->contextShopIds);

        return [
            'shop_association' => $shopIds,
            'is_messages_saving_enabled' => false,
        ];
    }
}
