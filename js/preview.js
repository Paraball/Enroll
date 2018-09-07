"use strict";
$(function () {

    let en = [];
    en['ev'] = true;
    en['nev'] = true;

    function able(flag, which) {
        en[which] = flag;
        if (!en['ev'] && !en['nev']) {
            $('#submit').attr('disabled', true).val('訊息不可為空');
        } else {
            $('#submit').attr('disabled', false).val('儲存');
        }
    }

    function preview(content, input) {
        $.post(
            'preview.php',
            {
                'content': content
            },
            function (data) {
                let $div = null;
                switch (input) {
                    case 'ev':
                        $div = $('#evOut');
                        $('#evOut').html('<p><span class="meta-name badge badge-success">已驗證的內容</span></p>' + data);
                        break;
                    case 'nev':
                        $div = $('#nevOut');
                        $('#nevOut').html('<p><span class="meta-name badge badge-danger">尚未驗證的內容</span></p>' + data);
                        break;
                }
                if (data) {
                    $div.css('display', 'block');
                    $div.find('p:not(:first)').remove();
                    $div.append(data);
                } else {
                    $div.css('display', 'none');
                }
                able(data != '', input);
            }
        );
    }

    function previewAll() {
        preview($('#evIn').val(), 'ev');
        preview($('#nevIn').val(), 'nev');
    }

    $('#evIn, #nevIn').bind('input propertychange', previewAll);

    previewAll();

});