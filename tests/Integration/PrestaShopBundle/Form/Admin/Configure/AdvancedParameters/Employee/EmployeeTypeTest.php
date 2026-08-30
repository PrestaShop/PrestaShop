<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Employee;

use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Employee\EmployeeType;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

class EmployeeTypeTest extends FormListenerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopId(1);
        $shopContextBuilder->setShopConstraint(\PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint::shop(1));

        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);
    }

    /**
     * The "Default page" choices depend on the selected role. When the role is changed to one that
     * can access a page the previous role could not, that page must be accepted on submit instead of
     * being rejected against the previously saved role.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/42015
     */
    public function testDefaultPageAccessibleOnlyToTheNewlySelectedProfileIsAccepted(): void
    {
        $superAdminProfileId = 1;
        $restrictedProfileId = 4; // Salesman

        // Determine, from the actual data, a page the super admin can pick but the restricted role
        // cannot, so the test does not depend on a specific tab being (in)accessible in the fixture.
        $restrictedPages = $this->defaultPageChoices($restrictedProfileId);
        $newProfilePage = current(array_diff($this->defaultPageChoices($superAdminProfileId), $restrictedPages));
        $restrictedPage = current($restrictedPages);

        self::assertNotFalse($newProfilePage, 'Expected a page accessible only to the super admin profile.');
        self::assertNotFalse($restrictedPage, 'Expected a page accessible to the restricted profile.');

        // Editing an employee currently on the restricted role, with a default page that role can access.
        $form = $this->createForm(EmployeeType::class, ['is_for_editing' => true], [
            'profile' => $restrictedProfileId,
            'default_page' => $restrictedPage,
        ]);

        // Change the role to the super admin one and pick a default page only that new role can access.
        $form->submit([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'language' => 1,
            'active' => 1,
            'profile' => $superAdminProfileId,
            'default_page' => $newProfilePage,
        ]);

        $defaultPage = $form->get('default_page');

        // Without the PRE_SUBMIT rebuild the value is not in the frozen (old role) choice list, so the
        // field fails to reverse-transform; with the fix the value is a valid choice for the new role.
        $this->assertTrue($defaultPage->isSynchronized());
        $this->assertSame($newProfilePage, $defaultPage->getData());
    }

    /**
     * @return int[] the selectable default page (tab) ids offered for the given profile
     */
    private function defaultPageChoices(int $profileId): array
    {
        $form = $this->createForm(EmployeeType::class, ['is_for_editing' => true], ['profile' => $profileId]);
        $choices = $form->get('default_page')->getConfig()->getOption('choices');

        $flat = [];
        array_walk_recursive($choices, static function ($value) use (&$flat): void {
            $flat[] = $value;
        });

        return $flat;
    }
}
