/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import FormFieldToggler, {ToggleType} from '@components/form/form-field-toggler';
import CountryMap from '@pages/country/country-map';

export default class ZipCodeManager {
  constructor() {
    this.initZipCodeToggler();
  }

  private initZipCodeToggler(): void {
    new FormFieldToggler({
      disablingInputSelector: CountryMap.isZipCodeNeededSwitch,
      targetSelector: CountryMap.zipCodeFormatInput,
      toggleType: ToggleType.availability,
    });
  }
}
