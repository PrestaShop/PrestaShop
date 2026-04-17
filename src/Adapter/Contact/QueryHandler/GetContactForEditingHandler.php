<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Contact\QueryHandler;

use Contact;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryHandler\GetContactForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult\EditableContact;
use PrestaShopException;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class GetContactForEditingHandler is responsible for getting the data for contact edit page.
 *
 * @internal
 */
#[AsQueryHandler]
final class GetContactForEditingHandler implements GetContactForEditingHandlerInterface
{
    /**
     * @var DataTransformerInterface
     */
    private $stringArrayToIntegerArrayDataTransformer;

    /**
     * @param DataTransformerInterface $stringArrayToIntegerArrayDataTransformer
     */
    public function __construct(DataTransformerInterface $stringArrayToIntegerArrayDataTransformer)
    {
        $this->stringArrayToIntegerArrayDataTransformer = $stringArrayToIntegerArrayDataTransformer;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ContactException
     */
    public function handle(GetContactForEditing $query)
    {
        try {
            $contact = new Contact($query->getContactId()->getValue());

            if (0 >= $contact->id) {
                throw new ContactNotFoundException(sprintf('Contact object with id %s was not found', var_export($query->getContactId()->getValue(), true)));
            }
            $editableContact = new EditableContact(
                $query->getContactId()->getValue(),
                $contact->name,
                $contact->email,
                (bool) $contact->customer_service,
                $contact->description,
                $this->stringArrayToIntegerArrayDataTransformer->reverseTransform($contact->getAssociatedShops())
            );
        } catch (PrestaShopException $e) {
            throw new ContactException(sprintf('An unexpected error occurred when retrieving contact with id %s', var_export($query->getContactId()->getValue(), true)), 0, $e);
        }

        return $editableContact;
    }
}
