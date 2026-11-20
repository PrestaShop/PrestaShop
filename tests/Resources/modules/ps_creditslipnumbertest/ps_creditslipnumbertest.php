<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class Ps_CreditSlipNumberTest extends Module
{
    public const FORMATTED_NUMBER = 'CN-TEST-0001';

    public function __construct()
    {
        $this->name = 'ps_creditslipnumbertest';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'Credit slip number formatting test module';
        $this->description = 'Reformats credit slip numbers for integration tests.';
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionCreditSlipNumberFormatted');
    }

    public function hookActionCreditSlipNumberFormatted(array $params)
    {
        return self::FORMATTED_NUMBER;
    }
}
