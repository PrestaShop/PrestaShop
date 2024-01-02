import fs from 'fs';
import path from 'path';
import https from 'https';

import ImportData from '@data/faker/import';

import {createObjectCsvWriter} from 'csv-writer';
import imgGen from 'js-image-generator';
import {getDocument, OPS, PDFDocumentProxy} from 'pdfjs-dist/legacy/build/pdf.js';
import {TextItem, TextMarkedContent} from 'pdfjs-dist/types/src/display/api';
import {RawImageData} from 'jpeg-js';

/**
 * @module FilesHelper
 * @description Helper to wrap functions that uses fs, pdfjs and js-image-generator libraries
 */
export default {
  /**
   * Delete File if exist
   * @param filePath {string|null} Filepath to delete
   * @return {Promise<void>}
   */
  async deleteFile(filePath: string|null): Promise<void> {
    if (filePath && fs.existsSync(filePath)) {
      fs.unlinkSync(filePath);
    }
  },

  /**
   * Delete files following pattern
   * @param path {string} Path
   * @param regex {RegExp} Pattern of files to remove
   * @return {Promise<void>}
   */
  async deleteFilePattern(path: string, regex: RegExp): Promise<void> {
    fs.readdirSync(path)
      .filter((f) => regex.test(f))
      .map((f) => fs.unlinkSync(path + f));
  },

  /**
   * Return files following pattern
   * @param path {string} Path
   * @param regex {RegExp} Pattern of files to remove
   * @return {Promise<void>}
   */
  async getFilesPattern(path: string, regex: RegExp): Promise<string[]> {
    return fs.readdirSync(path)
      .filter((f: string) => regex.test(f));
  },

  /**
   * Check if file was download in path
   * @param filePath {string|null} Filepath to check
   * @param attempt {number} Number of attempt to check for the file
   * @returns {Promise<boolean>}
   */
  async doesFileExist(filePath: string|null, attempt: number = 5000): Promise<boolean> {
    if (filePath === null) {
      return false;
    }

    let found: boolean = false;

    for (let i = 0; i <= attempt && !found; i += 100) {
      await (new Promise((resolve) => {
        setTimeout(resolve, 100);
      }));
      found = fs.existsSync(filePath);
    }

    return found;
  },

  /**
   * Get page text from PDF
   * @param pdf {PDFDocumentProxy} PDF loaded with pdfjs
   * @param pageNo {number} Page number of the PDF file
   * @return {Promise<string[]>} Text in PDF page
   */
  async getPageTextFromPdf(pdf: PDFDocumentProxy, pageNo: number): Promise<string[]> {
    const page = await pdf.getPage(pageNo);
    const tokenizedText = await page.getTextContent();

    return tokenizedText.items.map((token: TextItem | TextMarkedContent): string => ('str' in token ? token.str : ''));
  },

  /**
   * Check text in PDF
   * @param filePath {string|null} Path of the PDF file
   * @param text {string} Text to check on the file
   * @param deleteComma {boolean} True if we need to delete comma
   * @returns {Promise<boolean>}
   */
  async isTextInPDF(filePath: string|null, text: string, deleteComma: boolean = false): Promise<boolean> {
    if (filePath === null) {
      return false;
    }
    const pdf = await getDocument({
      url: filePath,
      standardFontDataUrl: path.join(path.dirname(__dirname), 'node_modules/pdfjs-dist/standard_fonts/'),
    }).promise;
    const maxPages = pdf.numPages;
    const pageTextPromises = [];

    for (let pageNo = 1; pageNo <= maxPages; pageNo += 1) {
      pageTextPromises.push(this.getPageTextFromPdf(pdf, pageNo));
    }

    const pageTexts = await Promise.all(pageTextPromises);

    if (deleteComma) {
      return (pageTexts.join(' ').split(',').join('').indexOf(text) !== -1);
    }

    return (pageTexts.join(' ').indexOf(text) !== -1);
  },

  /**
   * Get quantity of images on the PDF
   * @param filePath {string|null} FilePath of the PDF file
   * @return {Promise<number>}
   */
  async getImageNumberInPDF(filePath: string|null): Promise<number> {
    if (filePath === null) {
      return 0;
    }
    const pdf = await getDocument({
      url: filePath,
      standardFontDataUrl: path.join(path.dirname(__dirname), 'node_modules/pdfjs-dist/standard_fonts/'),
    }).promise;
    const nbrPages = pdf.numPages;
    let imageNumber = 0;

    for (let pageNo = 1; pageNo <= nbrPages; pageNo += 1) {
      const page = await pdf.getPage(nbrPages);
      /* eslint-disable no-loop-func */
      await page.getOperatorList().then(async (ops) => {
        for (let i = 0; i < ops.fnArray.length; i++) {
          if (ops.fnArray[i] === OPS.paintImageXObject) {
            imageNumber += 1;
          }
        }
      });
      /* eslint-enable no-loop-func */
    }

    return imageNumber;
  },
  /**
   * Generate report filename
   * @return {Promise<string>}
   */
  async generateReportFilename(): Promise<string> {
    const curDate = new Date();

    return `report-${
      curDate.toJSON().slice(0, 10)}-${
      curDate.getHours()}-${
      curDate.getMinutes()}-${
      curDate.getSeconds()}`;
  },
  /**
   * Create directory if not exist
   * @param path {string} Path of the directory to create
   * @return {Promise<void>}
   */
  async createDirectory(path: string): Promise<void> {
    if (!fs.existsSync(path)) await fs.mkdirSync(path);
  },
  /**
   * Create file with content
   * @param path {string} Path of the directory where to create
   * @param filename {string} Name of the file to create
   * @param content {string} Content to write on the file
   * @return {Promise<void>}
   */
  async createFile(path: string, filename: string, content: string): Promise<void> {
    await fs.writeFile(`${path}/${filename}`, content, (err: Error | null) => {
      if (err) {
        throw err;
      }
    });
  },
  /**
   * Check text in file
   * @param filePath {string|null} Filepath to check
   * @param textToCheckWith {string} Text to check on the file
   * @param ignoreSpaces {boolean} True to delete all spaces before the check
   * @param ignoreTimeZone {boolean} True to delete timezone string added to some image url
   * @param encoding {string} Encoding for the file
   * @return {Promise<boolean>}
   */
  async isTextInFile(
    filePath: string|null,
    textToCheckWith: string,
    ignoreSpaces: boolean = false,
    ignoreTimeZone: boolean = false,
    encoding: BufferEncoding = 'utf8',
  ): Promise<boolean> {
    if (filePath === null) {
      return false;
    }
    let fileText: string = await fs.readFileSync(filePath, {
      encoding,
    });
    let text: string = textToCheckWith;

    if (ignoreSpaces) {
      fileText = fileText.replace(/\s/g, '');
      text = text.replace(/\s/g, '');
    }
    if (ignoreTimeZone) {
      fileText = fileText.replace(/\?time=\d+/g, '');
      text = text.replace(/\?time=\d+/g, '');
    }

    return fileText.includes(text);
  },

  /**
   * Returns the extension of a file
   * @param path {string}
   * @return {string}
   */
  getFileExtension(path: string): string {
    return path.substring(path.lastIndexOf('.') + 1, path.length) || path;
  },

  /**
   * Generate image relative to the extension :
   * - JPG : js-image-generator
   * - PNG : copy existing file
   * - WebP : copy existing file
   * @param imageName {string} Filename/Filepath of the image
   * @param width {number} Width chosen for the image
   * @param height {number} Height chosen for the image
   * @param quality {number} Quality chosen for the image
   * @return {Promise<void>}
   */
  async generateImage(imageName: string, width: number = 200, height: number = 200, quality: number = 1): Promise<void> {
    const extension = this.getFileExtension(imageName);

    switch (extension) {
      case 'jpg': {
        await imgGen.generateImage(width, height, quality, (err: Error|null, image: RawImageData<Buffer>) => {
          if ('data' in image) {
            fs.writeFileSync(imageName, image.data);
          }
        });
        break;
      }
      case 'png': {
        fs.copyFile(`${path.dirname(__dirname)}/data/files/sample.png`, imageName, (err: NodeJS.ErrnoException|null) => {
          if (err) {
            throw err;
          }
        });
        break;
      }
      case 'webp': {
        fs.copyFile(`${path.dirname(__dirname)}/data/files/sample.webp`, imageName, (err: NodeJS.ErrnoException|null) => {
          if (err) {
            throw err;
          }
        });
        break;
      }
      default:
        throw new Error(`You can't generate image for ${extension.toUpperCase()} file.`);
    }
  },

  /**
   * Returns the filetype of a file based on the magic header
   * @param path {string} Path of the file
   * @return {Promise<string>}
   */
  async getFileType(path: string): Promise<string> {
    const buffer: Buffer = fs.readFileSync(path);

    // Jpeg
    if (buffer.length >= 3 && buffer[0] === 255 && buffer[1] === 216 && buffer[2] === 255) {
      return 'jpg';
    }

    // PNG
    if (buffer.length >= 8 && buffer[0] === 0x89 && buffer[1] === 0x50 && buffer[2] === 0x4E && buffer[3] === 0x47
      && buffer[4] === 0x0D && buffer[5] === 0x0A && buffer[6] === 0x1A && buffer[7] === 0x0A) {
      return 'png';
    }

    // WebP
    if (buffer.length >= 12 && buffer[8] === 87 && buffer[9] === 69 && buffer[10] === 66 && buffer[11] === 80) {
      return 'webp';
    }

    // ZIP
    if (buffer.length >= 4 && buffer[0] === 0x50 && buffer[1] === 0x4B && buffer[2] === 0x03 && buffer[3] === 0x04) {
      return 'zip';
    }

    return '';
  },

  /**
   * Rename files
   * @param oldPath {string|null} Old path of the file
   * @param newPath {string} New path of the file
   * @return {Promise<void>}
   */
  async renameFile(oldPath: string|null, newPath: string): Promise<void> {
    if (oldPath === null) {
      return;
    }
    await fs.rename(oldPath, newPath, (err) => {
      if (err) throw err;
    });
  },

  /**
   * Download a file from an URL to a path file
   * @param url {string} URL of the file
   * @param path {string} Path of the file
   * @return {Promise<void>}
   */
  async downloadFile(url: string, path: string): Promise<void> {
    await new Promise((resolve, reject): void => {
      const httpsAgent: https.Agent = new https.Agent({
        rejectUnauthorized: false,
      });

      https.get(
        url,
        {
          agent: httpsAgent,
        },
        (response): void => {
          const code = response.statusCode ?? 0;

          if (code >= 400) {
            reject(new Error(response.statusMessage));
            return;
          }

          // Handle redirects
          if (code > 300 && code < 400 && !!response.headers.location) {
            resolve(
              this.downloadFile(response.headers.location, path),
            );
            return;
          }

          // Save the file to disk
          const fileWriter: fs.WriteStream = fs
            .createWriteStream(path)
            .on('finish', (): void => {
              fileWriter.close();
              resolve({});
            });

          response.pipe(fileWriter);
        });
    });
  },

  /**
   * Create csv file
   * @param path {string} Path of the file
   * @param fileName {string} Name of the file to create
   * @param data {ImportData} Data to create csv file
   * @returns {Promise<void>}
   */
  async createCSVFile(path: string, fileName: string, data: ImportData): Promise<void> {
    await this.createFile(path, fileName, '');
    if ('header' in data && 'records' in data) {
      const csvWriter = createObjectCsvWriter({path: fileName, header: data.header, fieldDelimiter: ';'});
      await csvWriter.writeRecords(data.records);
    }
  },

  /**
   * Create a random SVG file
   * @param path {string} Path of the file
   * @param fileName {string} Name of the file to create
   * @returns {Promise<void>}
   */
  async createSVGFile(path: string, fileName: string): Promise<void> {
    const centerX = Math.floor(Math.random() * 15);
    const centerY = Math.floor(Math.random() * 15);
    const radius = Math.floor(Math.random() * 10);
    const style = `fill:rgb(${Math.floor(Math.random() * 255)},${Math.floor(Math.random() * 255)},`
      + `${Math.floor(Math.random() * 255)});`;

    let svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'
      + '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'
      + '<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">';

    for (let x = 0; x < 12; x++) {
      svg += `<circle cx="${centerX + (x * ((radius * 2) + 5))}" cy="${centerY}" r="${radius}" style="${style}"/>`;
    }
    svg += '</svg>';

    await fs.writeFile(`${path}/${fileName}`, svg, (err) => {
      if (err) throw err;
    });
  },

  /**
   * Get the root path
   * @returns {string}
   */
  getRootPath(): string {
    return path.resolve(__dirname, '../../../');
  },

  /**
   * Get the path of the file automatically generated
   * @param folderPath {string} Path of the folder where the file exists
   * @param filename {string} Path of the file automatically created
   * @returns {Promise<string>}
   */
  async getFilePathAutomaticallyGenerated(folderPath: string, filename: string): Promise<string> {
    return path.resolve(this.getRootPath(), folderPath, filename);
  },
};
