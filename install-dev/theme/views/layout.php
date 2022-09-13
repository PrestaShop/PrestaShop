<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <title><?php echo $this->translator->trans('PrestaShop Installation Assistant', [], 'Install'); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Cache" content="no store" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="robots" content="noindex" />
    <link rel="shortcut icon" href="theme/img/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="all" href="theme/view.css?version=<?php echo _PS_VERSION_; ?>" />

    <?php if ($this->language->getLanguage()->isRtl() == 'true') { ?>
      <link rel="stylesheet" type="text/css" media="all" href="theme/rtl.css" />
    <?php } ?>

    <script type="text/javascript" src="../js/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="../js/jquery/jquery-migrate-3.1.0.min.js"></script>
    <script type="text/javascript" src="../js/jquery/plugins/jquery.chosen.js"></script>

    <script src="theme/js/sprintf.min.js" integrity="sha512-pmG0OkYtZVB2EqETE5HPsEaok7sNZFfStp5rNdpHv0tGQjbt1z8Qjzhtx88/4wsttOtDwq5DZGJyKyzEe7ribg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="theme/js/zxcvbn.js" integrity="sha512-TZlMGFY9xKj38t/5m2FzJ+RM/aD5alMHDe26p0mYUMoCF5G7ibfHUQILq0qQPV3wlsnCwL+TPRNK4vIWGLOkUQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript" src="theme/js/install.js"></script>
    <?php if (file_exists(_PS_INSTALL_PATH_ . 'theme/js/' . self::getSteps()->current()->getName() . '.js')) { ?>
      <script type="text/javascript" src="theme/js/<?php echo self::getSteps()->current()->getName(); ?>.js?version=<?php echo _PS_VERSION_; ?>"></script>
    <?php } ?>
    <script type="text/javascript">
      var ps_base_uri = '<?php echo addslashes(__PS_BASE_URI__); ?>';
      var ps_version = '<?php echo addslashes(_PS_INSTALL_VERSION_); ?>';
    </script>
  </head>

  <body>
    <div id="container">

      <?php echo $this->getTemplate('header'); ?>

      <!-- Ajax loader animation -->
      <div id="loaderSpace">
        <div id="loader">&nbsp;</div>
      </div>

      <?php echo $this->getTemplate('menu'); ?>

      <!-- Page content -->
      <form id="mainForm" action="index.php" method="post">
        <div id="sheets" class="sheet shown">
          <div id="sheet_<?php echo self::getSteps()->current()->getName(); ?>" class="sheet shown clearfix">
            <div class="contentTitle">
              <h1><?php echo $this->translator->trans('Installation Assistant', [], 'Install'); ?></h1>
              <ul id="stepList_1" class="stepList clearfix">
                <?php foreach (self::getSteps() as $step) { ?>
                  <li <?php if ($this->isStepFinished($step->getName())) { ?>class="ok"<?php } ?>><?php echo $step; ?></li>
                <?php } ?>
              </ul>
            </div>
            <noscript>
              <h4 class="errorBlock" style="margin-bottom:10px">
                <?php echo $this->translator->trans('To install PrestaShop, you need to have JavaScript enabled in your browser.', [], 'Install'); ?>
                <a href="<?php echo $this->translator->trans('https://enable-javascript.com/', [], 'Install'); ?>" target="_blank" rel="noopener noreferrer">
                  <img src="theme/img/help.png" style="height:16px;width:16px" />
                </a>
              </h4>
            </noscript>

            <div>
              <?php echo $this->getContent(); ?>
            </div>
          </div>
        </div>

        <div id="buttons">
          <?php if (!$this->isLastStep()) { ?>
            <?php if ($this->next_button) { ?>
              <input id="btNext" class="button little" type="submit" name="submitNext" value="<?php echo $this->translator->trans('Next', [], 'Install'); ?>" />
            <?php } else { ?>
              <input id="btNext" class="button little disabled" type="submit" name="submitNext" value="<?php echo $this->translator->trans('Next', [], 'Install'); ?>" disabled="disabled" />
            <?php } ?>
          <?php } ?>

          <?php if (!$this->isFirstStep() && $this->previous_button) { ?>
            <input id="btBack" class="button little" type="submit" name="submitPrevious" value="<?php echo $this->translator->trans('Back', [], 'Install'); ?>" />
          <?php } ?>
        </div>
      </form>

      <?php echo $this->getHook('content-footer'); ?>
    </div>

    <?php echo $this->getTemplate('footer'); ?>
  </body>
</html>
