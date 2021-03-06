$(function() {
    $('a.show-multiple-input').bind('click', function () {
        var placeholder = $(this).data('placeholder');
        $('#' + placeholder + '-single').hide();
        $('#' + placeholder + '-single-input').prop('disabled', true);

        $('#' + placeholder + '-multiple').show();
        $('#' + placeholder + '-multiple-input').prop('disabled', false);

        $('#report-config-form').prop('method', 'POST');

        return false;
    });

    $('a.show-single-input').bind('click', function () {
        var placeholder = $(this).data('placeholder');
        $('#' + placeholder + '-single').show();
        $('#' + placeholder + '-single-input').prop('disabled', false);

        $('#' + placeholder + '-multiple').hide();
        $('#' + placeholder + '-multiple-input').prop('disabled', true);

        $('#report-config-form').prop('method', 'GET');

        return false;
    });
});
