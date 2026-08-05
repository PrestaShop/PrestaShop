<?php


class CustomerCreditRepository
{
    /**
     * Zwraca dane o kredycie klienta.
     */
    public function getCreditData(int $customerId): array
    {
        $sql = 'SELECT `credit_limit`, `current_debt` FROM `' . _DB_PREFIX_ . 'customer_credit` WHERE `id_customer` = ' . $customerId;
        $result = Db::getInstance()->getRow($sql);

        if (!$result) {
            return ['credit_limit' => 0.0, 'current_debt' => 0.0];
        }

        return [
            'credit_limit' => (float)$result['credit_limit'],
            'current_debt' => (float)$result['current_debt'],
        ];
    }

    /**
     * Zwraca kwotę dostępnych środków (Limit - Dług).
     */
    public function getAvailableCredit(int $customerId): float
    {
        $data = $this->getCreditData($customerId);
        return max(0, $data['credit_limit'] - $data['current_debt']);
    }

    /**
     * Aktualizuje limit kredytu dla klienta.
     */
    public function updateCreditLimit(int $customerId, float $newLimit): bool
    {
        // Sprawdzamy czy klient już istnieje w tabeli
        $exists = Db::getInstance()->getValue('SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'customer_credit` WHERE `id_customer` = ' . $customerId);

        if ($exists) {
            return Db::getInstance()->update(
                'customer_credit',
                ['credit_limit' => $newLimit, 'date_upd' => date('Y-m-d H:i:s')],
                '`id_customer` = ' . $customerId
            );
        } else {
            return Db::getInstance()->insert(
                'customer_credit',
                [
                    'id_customer' => $customerId,
                    'credit_limit' => $newLimit,
                    'current_debt' => 0,
                    'date_add' => date('Y-m-d H:i:s'),
                    'date_upd' => date('Y-m-d H:i:s'),
                ]
            );
        }
    }

    /**
     * Dodaje kwotę do aktualnego długu (po złożeniu zamówienia).
     */
    public function addDebt(int $customerId, float $amount): bool
    {
        $currentDebt = $this->getCreditData($customerId)['current_debt'];
        $newDebt = $currentDebt + $amount;

        return Db::getInstance()->update(
            'customer_credit',
            ['current_debt' => $newDebt, 'date_upd' => date('Y-m-d H:i:s')],
            '`id_customer` = ' . $customerId
        );
    }

    /**
     * Zmniejsza aktualne zadłużenie klienta o podaną kwotę (nie spada poniżej 0)
     */
    public function decreaseDebt(int $customerId, float $amount): bool
    {
        if ($customerId <= 0 || $amount <= 0) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'customer_credit`
                SET `current_debt` = GREATEST(0, `current_debt` - ' . (float)$amount . '),
                    `date_upd` = "' . $now . '"
                WHERE `id_customer` = ' . (int)$customerId;

        return Db::getInstance()->execute($sql);
    }
}
