export function showGrowl(type, message) {
  window.$.growl[type]({
    title: '',
    size: "large",
    message: message,
    duration: 1000
  });
};