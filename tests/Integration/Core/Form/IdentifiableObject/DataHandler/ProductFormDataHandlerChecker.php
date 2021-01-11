<?php

namespace Tests\Integration\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;

class ProductFormDataHandlerChecker implements FormDataHandlerInterface
{
    /**
     * @var FormDataHandlerInterface
     */
    private $productFormDataHandler;

    /**
     * @var ?array
     */
    private $lastCreateData;

    /**
     * @var ?int
     */
    private $lastUpdateId;

    /**
     * @var ?array
     */
    private $lastUpdateData;

    /**
     * ProductFormDataHandlerChecker constructor.
     *
     * @param FormDataHandlerInterface $productFormDataHandler
     */
    public function __construct(FormDataHandlerInterface $productFormDataHandler)
    {
        $this->productFormDataHandler = $productFormDataHandler;
    }

    public function create(array $data)
    {
        $this->lastCreateData = $data;

        return $this->productFormDataHandler->create($data);
    }

    public function update($id, array $data)
    {
        $this->lastUpdateId = $id;
        $this->lastUpdateData = $data;
        $this->productFormDataHandler->update($id, $data);
    }

    /**
     * @return array|null
     */
    public function getLastCreateData(): ?array
    {
        return $this->lastCreateData;
    }

    /**
     * @return int|null
     */
    public function getLastUpdateId(): ?int
    {
        return $this->lastUpdateId;
    }

    /**
     * @return array|null
     */
    public function getLastUpdateData(): ?array
    {
        return $this->lastUpdateData;
    }
}
