<?php use Mergado\Tools\CookieClass;

if($googleBadge['IS_INLINE']): ?>
    <?php if(CookieClass::functionalEnabled()): ?>
        <script src="https://apis.google.com/js/platform.js" async defer></script>
    <?php else: ?>
        <script>
            window.mmp.cookies.sections.functional.functions.googleReviewsBadge = function () {
                jQuery('body').append('<script src="https://apis.google.com/js/platform.js" async defer><\/script>');
            };
        </script>
    <?php endif; ?>
<?php else: ?>
    <!-- BEGIN GCR Badge Code -->
    <?php if(CookieClass::functionalEnabled()): ?>
        <script src="https://apis.google.com/js/platform.js?onload=renderBadge"
                async defer>
        </script>
    <?php else: ?>
        <script>
          window.mmp.cookies.sections.functional.functions.googleReviewsBadge = function () {
            jQuery('body').append('<script src="https://apis.google.com/js/platform.js?onload=renderBadge" async defer <\/script>');
          };
        </script>
    <?php endif; ?>

    <script>
      window.renderBadge = function() {
        var ratingBadgeContainer = document.createElement("div");
        document.body.appendChild(ratingBadgeContainer);
        window.gapi.load('ratingbadge', function() {
          window.gapi.ratingbadge.render(
              ratingBadgeContainer, {
                "merchant_id": <?=$googleBadge['MERCHANT_ID']?>,
                "position": "<?=$googleBadge['POSITION']?>"
              });
        });
      }
    </script>
    <!-- END GCR Badge Code -->
<?php endif ?>

<?php if($googleBadge['LANGUAGE'] !== 'automatically'): ?>
    <!-- BEGIN GCR Language Code -->
    <script>
      window.___gcfg = {
        lang: "<?= $googleBadge['LANGUAGE'] ?>"
      };
    </script>
    <!-- END GCR Language Code -->
<?php endif ?>