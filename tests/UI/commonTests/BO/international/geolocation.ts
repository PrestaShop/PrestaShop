import {
  utilsFile,
} from '@prestashop-core/ui-testing';
import fs from 'fs';

/**
 * Comment or not the geolocation check
 * @param status {boolean} True for comment or false
   * @return {void}
 */
function setGeolocationCheckCommented(status: boolean): void {
  let output: string;

  const file: string = `${utilsFile.getRootPath()}/classes/controller/FrontController.php`;
  const commentLine: string = "!in_array(Tools::getRemoteAddr(), ['127.0.0.1', '::1']) && ";

  const data = fs.readFileSync(file, 'utf8');

  if (status) {
    output = data.replace(
      commentLine,
      `/* ${commentLine} */`,
    );
  } else {
    output = data.replace(
      `/* ${commentLine} */`,
      commentLine,
    );
  }

  fs.writeFileSync(file, output, 'utf8');
}

export default setGeolocationCheckCommented;
