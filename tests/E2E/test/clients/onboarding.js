let CommonClient = require('./common_client');
global.startOnboarding = false;
global.mboModule = false;

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
          global.startOnboarding = false;
        }
      });
  }

  stopOnBoarding(selector) {
    if (global.isVisible) {
      return this.client
        .waitForExistAndClick(selector);
    }
  }

  checkMboModule(selector) {
    return this.client
      .isVisible(selector)
      .then((visible) => {
        if (visible) {
          global.mboModule = true;
        }
      });
  }
}

module.exports = OnBoarding;
