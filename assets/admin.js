jQuery(function ($) {
  var frame;
  var $select = $('#custom-login-logo-select');
  var $remove = $('#custom-login-logo-remove');
  var $id = $('#custom-login-logo-id');
  var $preview = $('#custom-login-logo-preview');
  var $previewWrap = $('.custom-login-logo-preview');
  var $colorPicker = $('.custom-login-logo-color');

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
      title: customLoginLogo.title,
      button: { text: customLoginLogo.button },
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
