/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {FormIframeModal} from '@components/modal';
import BusinessEntityListMap from './business-entity-list-map';

export default class EditStatusManager {
  constructor() {
    document.addEventListener('click', this.onEditStatusClick);
  }

  private onEditStatusClick = (event: Event): void => {
    const link = (event.target as HTMLElement).closest<HTMLAnchorElement>(BusinessEntityListMap.editStatusRowLink);

    if (!link) {
      return;
    }

    event.preventDefault();

    const {statusFormUrl} = link.dataset;

    if (!statusFormUrl) {
      return;
    }

    this.openModal(link, statusFormUrl);
  };

  private openModal(link: HTMLAnchorElement, formUrl: string): void {
    const modal = new FormIframeModal({
      id: BusinessEntityListMap.editStatusModalId,
      formSelector: BusinessEntityListMap.editStatusForm,
      formUrl: `${formUrl}${formUrl.includes('?') ? '&' : '?'}liteDisplaying=1`,
      closable: true,
      dialogStyle: {maxWidth: '540px'},
      modalTitle: link.dataset.modalTitle,
      confirmButtonLabel: link.dataset.confirmButtonLabel,
      closeButtonLabel: link.dataset.cancelButtonLabel,
      closeOnConfirm: false,
      onFormLoaded: (_form: HTMLFormElement, _formData: FormData, dataAttributes: DOMStringMap | null): void => {
        if (dataAttributes?.statusUpdated === '1') {
          modal.showLoading();
          window.location.reload();
        }
      },
      formConfirmCallback: (form: HTMLFormElement): void => {
        form.submit();
      },
    });

    modal.show();
  }
}
