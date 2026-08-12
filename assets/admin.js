jQuery(function ($) {
  var frame;
  var $select = $('#hdy-login-branding-select');
  var $remove = $('#hdy-login-branding-remove');
  var $id = $('#hdy-login-branding-id');
  var $preview = $('#hdy-login-branding-preview');
  var $previewWrap = $('.hdy-login-branding-preview');
  var $colorPicker = $('.hdy-login-branding-color');

  if ($.fn.wpColorPicker && $colorPicker.length) {
    $colorPicker.wpColorPicker();
  }

  $select.on('click', function (event) {
    event.preventDefault();

    if (frame) {
      frame.open();
      return;
    }

    frame = wp.media({
      title: hdyLoginBranding.title,
      button: { text: hdyLoginBranding.button },
      library: { type: 'image' },
      multiple: false
    });

    frame.on('select', function () {
      var attachment = frame.state().get('selection').first().toJSON();
      $id.val(attachment.id);
      $preview.attr('src', attachment.url);
      $previewWrap.removeClass('is-empty').addClass('is-set');
      $remove.prop('disabled', false);
    });

    frame.open();
  });

  $remove.on('click', function (event) {
    event.preventDefault();
    $id.val('');
    $preview.attr('src', '');
    $previewWrap.removeClass('is-set').addClass('is-empty');
    $remove.prop('disabled', true);
  });
});
