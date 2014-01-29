var App = {};

App.upload = {};

App.upload.init = function() {
    App.upload.update();
    $('#type-file').change(App.upload.update);
    $('#type-url').change(App.upload.update);
}

App.upload.update = function() {
    if ($('#type-file:checked').length) {
        $('#input-url').hide();
        $('#input-file').show();
    } else if ($('#type-url:checked').length) {
        $('#input-url').show();
        $('#input-file').hide();
    }
}

App.crop = {};

App.crop.init = function() {
    width = $('#container').width();
    height = $('#container').height();
    width_image = $('#layer-in').width();
    height_image = $('#layer-in').height();
    $('#layer-in').draggable().bind('drag', App.crop.drag);
    var x = (width > width_image) ? (width - width_image) / 2 : 0;
    var y = (height > height_image) ? (height - height_image) / 2 : 0;
    App.crop.update(x, y)
}

App.crop.drag = function(event, ui) {
    if (ui.position.top < 0) ui.position.top = 0;
    if (ui.position.left + width_image > width) ui.position.left = width - width_image;
    if (ui.position.left < 0) ui.position.left = 0;
    if (ui.position.top + height_image > height) ui.position.top = height - height_image;
    App.crop.update(ui.position.left, ui.position.top);
}

App.crop.update = function(x, y) {
    $('#layer-in').css('top', y);
    $('#y').val(y);
    $('#layer-in').css('left', x);
    $('#x').val(x);
    $('#layer-in').css('background-position', -x + ' ' + -y);
}

$(function() {
    App.upload.init();
    App.crop.init();
});
