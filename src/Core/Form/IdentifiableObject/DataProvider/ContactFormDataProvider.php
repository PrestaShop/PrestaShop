<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
