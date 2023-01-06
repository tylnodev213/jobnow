$(document).ready(function () {
    $('form').on('submit', function (e) {
        if ($(this).attr('show-loading') == 1) showLoading();
        $(this).off('submit').submit();
    });

    // upload image
    $(".input-image").change(function () {
        previewFile(this);
    });

    // clear preview file
    clearPreviewFile();

    //date picker
    $('.datepicker').datepicker({
        todayHighlight: true,
        autoclose: true,
        format: 'yyyy-mm-dd',
        language: 'ja'
    });

    // time picker
    $('.timepicker').timepicker({
        minuteStep: 1,
        showSeconds: false,
        showMeridian: false,
        defaultTime: ''
    });
});
