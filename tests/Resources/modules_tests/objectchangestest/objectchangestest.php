<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Records the changes passed to the object update changes hooks.
 */
class objectchangestest extends Module
{
    public const CLASS_HOOK = 'actionObjectTestsResourcesclassesTestableObjectModelUpdateChanges';

    /**
     * @var array<int, array{hook: string, changes: array}>
     */
    public static array $calls = [];

    public function __construct()
    {
        $this->name = 'objectchangestest';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName = 'Object changes test';
        $this->description = 'A module to test the object update changes hooks';
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionObjectUpdateChanges')
            && $this->registerHook(self::CLASS_HOOK);
    }

    public function hookActionObjectUpdateChanges(array $params): void
    {
        if ($params['object'] instanceof Tests\Resources\classes\TestableObjectModel) {
            self::$calls[] = ['hook' => 'actionObjectUpdateChanges', 'changes' => $params['changes']];
        }
    }

    public function hookActionObjectTestsResourcesclassesTestableObjectModelUpdateChanges(array $params): void
    {
        self::$calls[] = ['hook' => self::CLASS_HOOK, 'changes' => $params['changes']];
    }
}
