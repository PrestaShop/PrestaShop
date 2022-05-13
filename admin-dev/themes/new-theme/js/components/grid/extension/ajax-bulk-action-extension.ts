/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import {Grid} from '@PSTypes/grid';
import ProgressModal from '@components/modal/progress-modal';
import GridMap from '@components/grid/grid-map';
import Router from '@components/router';

import ClickEvent = JQuery.ClickEvent;

const {$} = window;

/**
 * Handles submit of grid actions
 */
export default class AjaxBulkActionExtension {
  private router = new Router();

  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getContainer()
      .on('click', GridMap.bulks.ajaxAction, (event: ClickEvent) => {
        this.submitForm($<HTMLInputElement>(event.currentTarget), $<HTMLInputElement>(GridMap.bulks.checkedCheckbox));
      });
  }

  private async submitForm($ajaxButton: JQuery<HTMLInputElement>, checkboxes: JQuery<HTMLInputElement>): Promise<void> {
    let stopProcess = false;
    const progressionTitle = $ajaxButton.data('progressTitle');
    const progressionMessage = $ajaxButton.data('progressMessage');
    const closeLabel = $ajaxButton.data('closeLabel');
    const stopProcessingLabel = $ajaxButton.data('stopProcessing');
    const errorsMessage = $ajaxButton.data('errorsMessage');
    const backToProcessingLabel = $ajaxButton.data('backToProcessing');
    const downloadErrorLogLabel = $ajaxButton.data('downloadErrorLog');
    const viewErrorLogLabel = $ajaxButton.data('viewErrorLog');
    const viewErrorTitle = $ajaxButton.data('viewErrorTitle');

    const modal = new ProgressModal({
      cancelCallback: () => {
        stopProcess = true;
      },
      closeCallback: () => {
        window.location.reload();
      },
      progressionTitle,
      progressionMessage,
      closeLabel,
      stopProcessingLabel,
      errorsMessage,
      backToProcessingLabel,
      downloadErrorLogLabel,
      viewErrorLogLabel,
      viewErrorTitle,
      total: checkboxes.length,
    });

    modal.show();
    let doneCount = 0;

    for (let i = 0; i < checkboxes.length; i += 1) {
      const checkbox = checkboxes[i];

      if (stopProcess) {
        break;
      }

      let data;
      try {
        // eslint-disable-next-line no-await-in-loop
        data = await this.callAjaxAction($ajaxButton, checkbox);
      } catch (e) {
        console.error(e);
        data = {error: `Something went wrong with ID ${checkbox.value}`};
      }

      doneCount += 1;
      modal.updateCount(doneCount);
      console.log('data', data);
      if (!data.success) {
        modal.addError(data.error ?? data.message);
      }
    }

    modal.completeProcess();
  }

  private callAjaxAction($ajaxButton: JQuery<HTMLInputElement>, checkbox: HTMLInputElement): JQuery.jqXHR {
    const requestParamName: string | undefined = $ajaxButton.data('requestParamName');
    const routeParams: Record<string, any> = $ajaxButton.data('routeParams') ?? {};
    const data: Record<string, any> = {};
    console.log('requestParamName', requestParamName);
    if (requestParamName) {
      data[requestParamName] = checkbox.value;
    }

    return $.ajax({
      url: this.router.generate($ajaxButton.data('ajax-url'), routeParams),
      type: 'POST',
      data,
      success(successData:any) {
        return successData;
      },
    });
  }
}
