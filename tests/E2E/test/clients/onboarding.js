let CommonClient = require('./common_client');
global.startOnboarding = false;
class OnBoarding extends CommonClient {

  checkResumeAndStartButton(onBoardingModal, resumeButton) {
    return this.client
      .isVisible(onBoardingModal)
      .then((visible) => {
        if (visible) {
          global.startOnboarding = true;
        }
      })
      .then(() => this.client.isVisible(resumeButton))
      .then((visible) => {
        if (visible) {
          global.startOnboarding = true;
          this.client.click(resumeButton);
        }
      });
  }

  stopOnBoarding(selector) {
    if (global.isVisible) {
      return this.client
        .waitForExistAndClick(selector);
    }
  }
}

module.exports = OnBoarding;
