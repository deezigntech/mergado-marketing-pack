(function ($) {
  'use strict';

  $(function() {
    // Tab control
    $('.mmp_tabs__menu li').on('click', function(e) {
      e.preventDefault();
      $('.mmp_tabs li.active').removeClass('active');
      $('.mmp_tabs__tab.active').removeClass('active');
      $(this).addClass('active');
      $('[data-mmp-tab="' + $(this).children('a').attr('data-mmp-tab-button') + '"]').addClass('active');
    });

    $('[data-mmp-tab-button]').on('click', function () {
      var urlParams = new URLSearchParams(window.location.search);
      urlParams.set('mmp-tab', $(this).attr('data-mmp-tab-button'));
      window.history.pushState('', '',  'admin.php?' + urlParams);
    });

    if (window.location.href.indexOf("page=mergado-cookies") > -1 && window.location.href.indexOf("mmp-tab=") <= -1) {
      jQuery('[data-mmp-tab-button="cookies"]').click();
    }
  });
})(jQuery)
