<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class ContactDetailsFormHandler implements FormHandlerInterface
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly ContactDetailsFormDataProvider $dataProvider,
    ) {
    }

    public function getForm(): FormInterface
    {
        return $this->formFactory
            ->createBuilder()
            ->add('contact_details', ContactDetailsType::class, ['label' => false])
            ->setData(['contact_details' => $this->dataProvider->getData()])
            ->getForm();
    }

    public function save(array $data): array
    {
        return $this->dataProvider->setData($data['contact_details'] ?? $data);
    }
}
