<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Test fixture for the actionSubmitAccountBefore hook.
 *
 * The value the hook method returns is what distinguishes a module that merely observes the hook
 * from one that vetoes the account creation, so the test drives it through a static property.
 */
class SubmitAccountHookTest extends Module
{
    /**
     * Returned by the hook. null stands for the very common module that observes without returning
     * anything at all; false is an explicit veto.
     *
     * @var mixed
     */
    public static $hookReturnValue = null;

    public function __construct()
    {
        $this->name = 'submitaccounthooktest';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'Submit account hook test';
        $this->description = 'Test fixture returning a configurable value from actionSubmitAccountBefore.';
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionSubmitAccountBefore');
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function hookActionSubmitAccountBefore($params)
    {
        return self::$hookReturnValue;
    }
}
