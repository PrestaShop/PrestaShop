// Import pages
import themeAndLogoBasePage from '@pages/BO/design/themeAndLogo/themeAndLogo/themeAndLogoBasePage';

// Import data
import ModuleData from '@data/faker/module';

import {Page} from 'playwright';

/**
 * Pages configuration page, contains functions that can be used on the page
 * @class
 * @extends themeAndLogoBasePage
 */
class PagesConfigurationPage extends themeAndLogoBasePage {
  public readonly pageTitle: string;

  public readonly successMessage: string;

  private readonly moduleBlock: (moduleTag: string) => string;

  private readonly actionModuleButton: (moduleTag: string, action: string) => string;

  private readonly configureModuleButton: (moduleTag: string) => string;

  private homePageModal: string;

  private readonly actionsDropdownButton: (moduleTag: string) => string;

  private readonly actionModuleButtonInDropdownList: (moduleTag: string, action: string) => string;

  private readonly modalConfirmButton: (action: string) => string;

  private readonly modalConfirmAction: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on advanced customization page
   */
  constructor() {
    super();

    this.pageTitle = `Pages Configuration • ${global.INSTALL.SHOP_NAME}`;
    this.successMessage = 'Action on the module successfully completed';

    // Module actions
    this.homePageModal = '#homepageModal';
    this.moduleBlock = (moduleTag: string) => `${this.homePageModal} div[data-module_name=${moduleTag}]`;
    this.actionModuleButton = (moduleTag: string, action: string) => `${this.moduleBlock(moduleTag)
    } button.module_action_menu_${action}`;
    this.configureModuleButton = (moduleTag: string) => `${this.moduleBlock(moduleTag)
    } div.module-actions a[href*='/action/configure']`;

    // Module actions in dropdown selectors
    this.actionsDropdownButton = (moduleTag: string) => `${this.homePageModal
    } div.src_parent_${moduleTag} button.dropdown-action`;

    this.actionModuleButtonInDropdownList = (moduleTag: string, action: string) => `${this.homePageModal
    } div.src_parent_${moduleTag} button.module_action_menu_${action}`;

    // Modal confirmation selectors
    this.modalConfirmAction = '#moduleActionModal';
    this.modalConfirmButton = (action: string) => `${this.modalConfirmAction} span.${action}.action_available`;
  }

  /* Methods */

  /**
   * Install/uninstall/enable/disable/reset module
   * @param page {Page} Browser tab
   * @param module {ModuleData} Module data to install/uninstall/enable/disable/reset
   * @param action {string} Action install/uninstall/enable/disable/reset
   * @return {Promise<string | null>}
   */
  async setActionInModule(page: Page, module: ModuleData, action: string): Promise<string | null> {
    await this.closeGrowlMessage(page);

    if (await this.elementVisible(page, this.actionModuleButton(module.tag, action), 1000)) {
      await this.waitForSelectorAndClick(page, this.actionModuleButton(module.tag, action));
    } else {
      await page.locator(this.actionsDropdownButton(module.tag)).click();
      await this.waitForSelectorAndClick(page, this.actionModuleButtonInDropdownList(module.tag, action));
    }

    if (action === 'disable' || action === 'uninstall' || action === 'reset') {
      await this.waitForSelectorAndClick(page, this.modalConfirmButton(action));
    }

    return this.getGrowlMessageContent(page);
  }
}

export default new PagesConfigurationPage();
