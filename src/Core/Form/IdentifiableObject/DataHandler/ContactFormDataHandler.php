<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\EditContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Class ContactFormDataHandler is responsible for handling create and update of contact form.
 */
final class ContactFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DomainException
     */
    public function create(array $data)
    {
        $addContactCommand = (new AddContactCommand($data['title'], $data['is_messages_saving_enabled']))
            ->setLocalisedDescription($data['description'])
            ->setShopAssociation(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        if ($data['email']) {
            $addContactCommand->setEmail($data['email']);
        }

        /** @var ContactId $result */
        $result = $this->commandBus->handle($addContactCommand);

        return $result->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws DomainException
     */
    public function update($contactId, array $data)
    {
        $editContactCommand = (new EditContactCommand((int) $contactId))
            ->setLocalisedTitles($data['title'])
            ->setIsMessagesSavingEnabled($data['is_messages_saving_enabled'])
            ->setLocalisedDescription($data['description'])
            ->setShopAssociation(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        if ($data['email']) {
            $editContactCommand->setEmail($data['email']);
        }

        $this->commandBus->handle($editContactCommand);
    }
}
