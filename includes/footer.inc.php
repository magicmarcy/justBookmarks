<?php
$userid = $_SESSION['userdata']['ID'];

if (isset($userid)) {

  $footerParameter = getFooterParameter($userid);

  $showFooter = $footerParameter[SHOW_FOOTER];
  $showDonoLink = $footerParameter[SHOW_DONO_LINK];
  $showAboutLink = $footerParameter[SHOW_ABOUT_LINK];
  $showDatenschutzLink = $footerParameter[SHOW_DATENSCHUTZ_LINK];
  $showImpressumLink = $footerParameter[SHOW_IMPRESSUM_LINK];

  Logger::trace("Footer-Data: SHOWFOOTER=" . $showFooter);
  Logger::trace("Footer-Data: SHOWDONOLINK=". $showDonoLink . ' SHOWABOUTLINK=' . $showAboutLink . ' SHOWDATENSCHUTZLINK=' . $showDatenschutzLink . ' SHOWIMPRESSUMLINK=' . $showImpressumLink);

  if ($showFooter && ($showDonoLink || $showAboutLink || $showDatenschutzLink || $showImpressumLink)) {
    echo '<div class="footer-section">';

    if ($showDonoLink) {
      echo '  <div class="float-left">';
      echo '    <div class="donation-text-box">';
      echo '      <span class="donoation-text-box-text">It\'s hard to fight evil but the little things, like a cup of coffee, really helps. Right? -> <a class="donation-text-link" href="' . BUYMEACOFFEELINK . '" target="_blank">buymeacoffee</a></span>';
      echo '    </div>';
      echo '  </div>';
    }

    if ($showAboutLink || $showDatenschutzLink || $showImpressumLink) {
      echo '  <div class="float-right">';
      echo '    <div class="internal-links">';

      if ($showAboutLink) {
        echo '      <div class="about-box float-right">';
        echo '        <span class="about-box-text"><a class="about-text-link" href="" target="">About</a></span>';
        echo '      </div>';
      }

      if ($showDatenschutzLink) {
        echo '      <div class="datenschutz-box float-right">';
        echo '        <span class="datenschutz-box-text"><a class="datenschutz-text-link" href="" target="">Datenschutz</a></span>';
        echo '      </div>';
      }

      if ($showImpressumLink) {
        echo '      <div class="impressum-box float-right">';
        echo '        <span class="impressum-box-text"><a class="impressum-text-link" href="" target="">Impressum</a></span>';
        echo '      </div>';
      }

      echo '    </div>';
      echo '  </div>';
    }

    echo '</div>';
  }
}

