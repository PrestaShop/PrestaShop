import LanguageData from '@data/faker/language';

export default {
  english: new LanguageData({
    id: 1,
    name: 'English (English)',
    isoCode: 'en',
    languageCode: 'en-us',
    dateFormat: 'm/d/Y',
    fullDateFormat: 'm/d/Y H:i:s',
    isRtl: false,
    enabled: true,
  }),
  french: new LanguageData({
    id: 2,
    name: 'Français (French)',
    isoCode: 'fr',
    languageCode: 'fr',
    dateFormat: 'd/m/Y',
    fullDateFormat: 'd/m/Y H:i:s',
    isRtl: false,
    enabled: true,
  }),
  spanish: new LanguageData({
    name: 'Español (Spanish)',
    isoCode: 'es',
    languageCode: 'es-es',
    dateFormat: 'd/m/Y',
    fullDateFormat: 'd/m/Y H:i:s',
    flag: 'flag.png',
    noPicture: 'noPicture.png',
    isRtl: false,
    enabled: true,
  }),
  deutsch: new LanguageData({
    name: 'Deutsch (German)',
    isoCode: 'de',
    languageCode: 'de-de',
    dateFormat: 'd.m.Y',
    fullDateFormat: 'd.m.Y H:i:s',
    flag: 'flag.png',
    noPicture: 'noPicture.png',
    isRtl: false,
    enabled: true,
  }),
  turkish: new LanguageData({
    name: 'Türkçe (Turkish)',
    isoCode: 'tr',
    languageCode: 'tr-tr',
    dateFormat: 'Y-m-d',
    fullDateFormat: 'Y-m-d H:i:s',
    flag: 'flag.png',
    noPicture: 'noPicture.png',
    isRtl: false,
    enabled: true,
  }),
  spanishAR: new LanguageData({
    name: 'Español AR (Spanish)',
    isoCode: 'ag',
    languageCode: 'es-ar',
    dateFormat: 'Y-m-d',
    fullDateFormat: 'Y-m-d H:i:s',
    flag: 'flag.png',
    noPicture: 'noPicture.png',
    isRtl: false,
    enabled: true,
  }),
  dutch: new LanguageData({
    name: 'Nederlands (Dutch)',
    isoCode: 'nl',
    languageCode: 'nl-nl',
    dateFormat: 'd-m-Y',
    fullDateFormat: 'd-m-Y H:i:s',
    flag: 'flag.png',
    noPicture: 'noPicture.png',
    isRtl: false,
    enabled: true,
  }),
  portuguese: new LanguageData({
    name: 'Português BR (Portuguese)',
    isoCode: 'br',
    languageCode: 'pt-br',
    dateFormat: 'Y-m-d',
    fullDateFormat: 'Y-m-d H:i:s',
    flag: 'flag.png',
    noPicture: 'noPicture.png',
    isRtl: false,
    enabled: true,
  }),
  croatian: new LanguageData({
    name: 'Hrvatski (Croatian)',
    isoCode: 'hr',
    languageCode: 'hr-hr',
    dateFormat: 'Y-m-d',
    fullDateFormat: 'Y-m-d H:i:s',
    flag: 'flag.png',
    noPicture: 'noPicture.png',
    isRtl: false,
    enabled: true,
  }),
  simplifiedChinese: new LanguageData({
    name: '中文 (Simplified Chinese)',
    isoCode: 'zh',
    languageCode: 'zh-cn',
    dateFormat: 'Y-m-d',
    fullDateFormat: 'Y-m-d H:i:s',
    flag: 'flag.png',
    noPicture: 'noPicture.png',
    isRtl: false,
    enabled: true,
  }),
  traditionalChinese: new LanguageData({
    name: '繁體中文 (Traditional Chinese)',
    isoCode: 'tw',
    languageCode: 'zh-tw',
    dateFormat: 'Y-m-d',
    fullDateFormat: 'Y-m-d H:i:s',
    flag: 'flag.png',
    noPicture: 'noPicture.png',
    isRtl: false,
    enabled: true,
  }),
};
