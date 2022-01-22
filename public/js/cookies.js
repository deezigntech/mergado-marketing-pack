window.mmp.cookies.functions = {
  enableFunctional: function () {
    if (Object.keys(window.mmp.cookies.sections.functional.functions).length > 0) {
      Object.values(window.mmp.cookies.sections.functional.functions).forEach(function (el) {
        el();
      });
    }
  },
  enableAnalytical: function () {
    if (Object.keys(window.mmp.cookies.sections.analytical.functions).length > 0) {
      Object.values(window.mmp.cookies.sections.analytical.functions).forEach(function (el) {
        el();
      });
    }
  },
  enableAdvertisement: function () {
    if (Object.keys(window.mmp.cookies.sections.advertisement.functions).length > 0) {
      Object.values(window.mmp.cookies.sections.advertisement.functions).forEach(function (el) {
        el();
      });
    }
  },
  checkAndSetCookies: function () {
    setTimeout(function () {
      var BreakException = {};

      try {
        Object.values(window.mmp.cookies.sections.functional.names).forEach(function (val) {

          if (window.mmp.cookies.functions.isCookieEnabled(val)) {
            window.mmp.cookies.functions.enableFunctional();
            throw BreakException;
          }
        });
      } catch (e) {
        if (e !== BreakException)  {
          throw e;
        }
      }

      try {
        Object.values(window.mmp.cookies.sections.analytical.names).forEach(function (val) {

          if (window.mmp.cookies.functions.isCookieEnabled(val)) {
            window.mmp.cookies.functions.enableAnalytical();
            throw BreakException;
          }
        });
      } catch (e) {
        if (e !== BreakException)  {
          throw e;
        }
      }

      try {
        Object.values(window.mmp.cookies.sections.advertisement.names).forEach(function (val) {

          if (window.mmp.cookies.functions.isCookieEnabled(val)) {
            window.mmp.cookies.functions.enableAdvertisement();
            throw BreakException;
          }
        });
      } catch (e) {
        if (e !== BreakException)  {
          throw e;
        }
      }
    }, 500);
  },
  isCookieEnabled: function (name) {
    let positiveOptions = ["yes", "true", true, 1, "1"];

    let item = window.mmp.cookies.functions.getCookie(name);

    if (item !== "") {
      if (positiveOptions.includes(item)) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  },
  getCookie: function (cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(";");
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == " ") {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }
};

// Check on cookies change
document.addEventListener('DOMContentLoaded', function () {
  var btns = jQuery('#wt-cli-privacy-save-btn, #wt-cli-accept-all-btn');

  btns.each(function () {
    this.addEventListener('click', function () {
      window.mmp.cookies.functions.checkAndSetCookies();
    });
  });
});