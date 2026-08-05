<?php

require_once __DIR__ . '/../../src/Domain/CustomerCreditRepository.php';

class AdvancecreditMycreditModuleFrontController extends ModuleFrontController
{
    public $auth = true; // Wymaga zalogowania

    public function initContent()
    {
        parent::initContent();

        $customerId = (int) $this->context->customer->id;
        $creditRepository = new CustomerCreditRepository();

        $creditData = $creditRepository->getCreditData($customerId);
        $availableCredit = $creditRepository->getAvailableCredit($customerId);

        $this->context->smarty->assign([
            'credit_limit' => $creditData['credit_limit'],
            'current_debt' => $creditData['current_debt'],
            'available_credit' => $availableCredit,
        ]);

        $this->setTemplate('module:advancecredit/views/templates/front/mycredit.tpl');
    }
}
