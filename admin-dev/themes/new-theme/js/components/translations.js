export default async function getTranslations() {
  try {
    const response = await fetch(window.data.translationUrl);
    const datas = response.json();

    return datas;
  } catch (error) {
    return error;
  }
}
