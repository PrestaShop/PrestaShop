<?php

namespace PrestaShop\PrestaShop\Adapter\CustomerService;

use PrestaShop\PrestaShop\Core\CustomerService\CustomerThreadStatusProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerThreadCustomerThreadStatusProvider implements CustomerThreadStatusProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getStatuses(): array
    {
        return [
            CustomerThreadStatus::OPEN => $this->translator->trans('Open', [], 'Admin.Catalog.Feature'),
            CustomerThreadStatus::CLOSED => $this->translator->trans('Closed', [], 'Admin.Catalog.Feature'),
            CustomerThreadStatus::PENDING_1 => $this->translator->trans('Pending 1', [], 'Admin.Catalog.Feature'),
            CustomerThreadStatus::PENDING_2 => $this->translator->trans('Pending 2', [], 'Admin.Catalog.Feature'),
        ];
    }
}
