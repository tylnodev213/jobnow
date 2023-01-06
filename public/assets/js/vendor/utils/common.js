function showLoading() {
    $.LoadingOverlay("show", {zIndex: 999999999});
}

function hideLoading() {
    $.LoadingOverlay("hide");
}

function showSuccessFlash(messages) {
    var html = '<hr><div class="row"><div class="col-md-12"><ul class="col-md-12 alert alert-success">';
    if (typeof messages === 'string') {
        html += '<li><i class="fa fa-check"></i><strong>' + messages + '</strong></li>';
    } else {
        messages.forEach(function (e) {
            html += '<li><i class="fa fa-check"></i><strong>' + e + '</strong></li>';
        });
    }
    html += '</ul></div></div>';
    $('#success_msg').html(html);
}

function showErrorFlash(messages, parent, scroll) {
    var html = '<div class="alert alert-danger"><ul>';
    if (typeof messages === 'string') {
        html += '<li>' + messages + '</li>';
    } else {
        messages.forEach(function (e) {
            html += '<li>' + e + '</li>';
        });
    }

    html + '</ul></div>';
    $(parent).find('#error_msg').html(html);
    typeof scroll == 'undefined' ? scrollToTop() : '';
}

function clearFlash() {
    clearSuccessFlash();
    clearErrorFlash();
}

function clearErrorFlash() {
    $('[id="error_msg"]').html('');
}

function clearSuccessFlash() {
    $('[id="success_msg"]').html('');
}

function redirect(url) {
    //todo validate limit redriect by url and tab session
    return url == '' ? window.location.reload() : window.location.href = url;
}

function isUrl(url) {
    url = url.replace('https://', '').replace('http://');
    var currentUrl = getCurrentUrl();
    return currentUrl.indexOf(url) !== -1;
}

function previewFile(input) {
    if (!input.files || !input.files[0]) {
        return false;
    }

    let inputName = $(input).attr('name');
    let previewId = '#preview-file-' + inputName;

    if (!validateFile(input)) {
        $(previewId).empty();
        $(input).closest('.form-group').find('.input-file').val('');

        return false;
    }

    clearFlash();

    $(previewId).empty();

    createPreview(input.files[0], $(previewId));

}

function isFileType(file, fileType) {
    var type = file.type.replace(/\/.*$/, '');

    return type === fileType;
}

function createPreview(file, container) {
    var $wrapper = $(container);
    var fileName = file.name !== undefined ? file.name : '';
    var showRemoveType = $wrapper.closest('.form-group').find('.input-file').attr('show_remove_type');
    var previewImageClass = $wrapper.closest('.form-group').find('.input-file').attr('preview-image-class');
    var removeFile = '';

    if (typeof showRemoveType !== 'undefined') {
        if (showRemoveType === 'button') {
            removeFile = '<button type="button" class="btn btn-danger remove-file">削除</button>';
        } else if (showRemoveType === 'icon') {
            removeFile = '<i class="remove-file la la-close"></i>';
        }
    }

    if (isFileType(file, 'image')) {
        var reader = new FileReader();

        reader.onload = function (e) {
            // create temporary img tag
            var $img = $(document.createElement('img'));
            $img.attr('src', e.target.result);

            if (typeof previewImageClass !== 'undefined') {
                $img.attr('class', previewImageClass);
            }

            // change file name upload
            $wrapper.addClass('preview-image');
            $wrapper.append($img);
            $wrapper.append(removeFile);
            clearPreviewFile();
        };

        reader.readAsDataURL(file);
    } else if (isFileType(file, 'video')) {
        var $video = $(document.createElement('video'));
        $video.attr('controls', '');
        $video.attr('height', '250');

        var $source = $(document.createElement('source'));
        $source.attr('src', URL.createObjectURL(file));

        $video.append($source);
        $wrapper.removeClass('preview-image').append($video);
        $wrapper.append(removeFile);
        clearPreviewFile();
        $video.load();
    } else {
        $wrapper.removeClass('preview-image').empty().append(fileName);
    }
}

function clearPreviewFile() {
    $('.remove-file').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.form-group').find('.input-file').val('');

        let defaultImage = $(this).closest('.form-group').find('.input-file').attr('default-image');
        let fileUploaded = $(this).closest('.form-group').find('.file-uploaded').val();

        if (typeof defaultImage != 'undefined') {
            $(this).closest('.form-group').find('.preview-image img').attr('src', defaultImage);
        }

        if (typeof fileUploaded != 'undefined') {
            $(this).closest('.form-group').find('.file-uploaded').val('');
        }

        $(this).remove();
    });
}

function validateFile(input) {
    var msg;
    var sizeAllow = input.getAttribute('size');
    var extAllow = input.getAttribute('ext');
    var showErrorInput = input.getAttribute('show_error_input');
    var extsAllow = extAllow.split(',');
    sizeAllow = sizeAllow.split(',');
    var minSize = parseFloat(sizeAllow[0]);
    var maxSize = parseFloat(sizeAllow[1]);

    var file = input.files[0];
    var size = file.size / 1024 / 1024;
    var extension = input.value.substr(input.value.lastIndexOf('.') + 1).toLowerCase();
    var label = input.getAttribute('data-label');

    // file type
    var modal = $(input).closest('.modal').length ? $(input).closest('.modal') : $('body');
    $(input).closest('.form-group').find('.show-error-message').removeClass('d-block').html('');
    $(input).closest('.form-group').find('.btn-upload').removeClass('upload-error');

    if (extension.length <= 0 || extsAllow.indexOf(extension) === -1) {
        msg = validateFileMsg._g('mimes').replace(':attribute', label).replace(':values', extAllow);
        showInvalidMessage(input, msg, showErrorInput, modal);
        return false;
    }
    // size
    if (size < minSize) {
        msg = validateFileMsg._g('min').replace(':attribute', label).replace(':min', minSize);
        showInvalidMessage(input, msg, showErrorInput, modal);
        return false;
    }
    if (size > maxSize) {
        msg = validateFileMsg._g('max').replace(':attribute', label).replace(':max', maxSize);
        showInvalidMessage(input, msg, showErrorInput, modal);
        return false;
    }

    return true;
}

function showInvalidMessage(input, msg, showErrorInput, modal) {
    if (showErrorInput !== typeof undefined && showErrorInput) {
        $(input).closest('.form-group').find('.show-error-message').addClass('d-block').html('<p class="error">' + msg + '</p>');
        $(input).closest('.form-group').find('.btn-upload').addClass('upload-error');
    } else {
        showErrorFlash(msg, modal);
    }
}

function fillForm(val) {
    if (val === undefined) {
        val = 1;
    }
    $('form').first().find('input[type!="hidden"],select,textarea').val(val).trigger('change');
}
