<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Configure\ShopParameters\General;

use PrestaShop\PrestaShop\Core\Feature\Enum\ShopModeEnum;
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\General\PreferencesType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Tests\Integration\PrestaShopBundle\Form\AbstractFormTester;

class PreferencesTypeTest extends AbstractFormTester
{
    public function testTheSslSwitchIsOfferedOverHttps(): void
    {
        $form = $this->createPreferencesForm(true);

        $this->assertTrue($form->has('enable_ssl'));
        $this->assertFalse($form->get('enable_ssl')->getConfig()->getDisabled());
    }

    public function testTheSslSwitchIsShownButNotSwitchableOverHttp(): void
    {
        $form = $this->createPreferencesForm(false);

        // Hiding it left the page with an "Enable SSL" heading and no control under it.
        $this->assertTrue($form->has('enable_ssl'));
        $this->assertTrue($form->get('enable_ssl')->getConfig()->getDisabled());
    }

    /**
     * The switch is not switchable over http:// because turning SSL on from a shop that cannot
     * serve HTTPS locks its own back office out, so a submission from there must not change it.
     */
    public function testASubmissionOverHttpCannotChangeTheSslSetting(): void
    {
        $form = $this->createPreferencesForm(false, ['enable_ssl' => true]);

        $submitted = $this->submittedValues();
        $submitted['enable_ssl'] = '0';
        $form->submit($submitted);

        $this->assertTrue($form->getData()['enable_ssl']);
    }

    public function testASubmissionOverHttpsChangesTheSslSetting(): void
    {
        $form = $this->createPreferencesForm(true, ['enable_ssl' => true]);

        $submitted = $this->submittedValues();
        $submitted['enable_ssl'] = '0';
        $form->submit($submitted);

        $this->assertFalse($form->getData()['enable_ssl']);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createPreferencesForm(bool $secure, array $overrides = []): FormInterface
    {
        $server = ['HTTP_HOST' => 'shop.test', 'REQUEST_URI' => '/admin/configure/shop'];
        if ($secure) {
            $server['HTTPS'] = 'on';
        }
        self::getContainer()->get('request_stack')->push(new Request([], [], [], [], [], $server));

        // WHY setData(): it is the sequence the form handler runs (create, then setData from the provider).
        $form = $this->createForm(PreferencesType::class);
        $form->setData(array_merge($this->formData(), $overrides));

        return $form;
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'enable_ssl' => false,
            'enable_token' => true,
            PreferencesType::SHOP_MODE => ShopModeEnum::SHOP_MODE_B2C_ONLY,
            'allow_html_iframes' => false,
            'use_htmlpurifier' => true,
            'price_round_mode' => '2',
            'price_round_type' => '1',
            'display_suppliers' => true,
            'display_manufacturers' => true,
            'display_best_sellers' => false,
            'multishop_feature_active' => false,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function submittedValues(): array
    {
        return [
            'enable_token' => '1',
            PreferencesType::SHOP_MODE => ShopModeEnum::SHOP_MODE_B2C_ONLY->value,
            'allow_html_iframes' => '0',
            'use_htmlpurifier' => '1',
            'price_round_mode' => '2',
            'price_round_type' => '1',
            'display_suppliers' => '1',
            'display_manufacturers' => '1',
            'display_best_sellers' => '0',
            'multishop_feature_active' => '0',
        ];
    }
}
