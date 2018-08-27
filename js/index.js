"use strict";
(function () {
    let $selCt = $('#county');
    let $selDt = $('#district');
    let $selCn = $('#cccd');

    $selCt.change(function () {
        let selOpt = $selCt.children('option:selected').val();
        $selDt.empty();
        $.post('search.php', {
            type: 'council',
            county: selOpt,
        }, function (data) {
            data = JSON.parse(data);
            let i = 1;
            for (let d of data) {
                $selDt.append(
                    $('<option></option>').attr('value', i).html('【' + (i < 10 ? '0' + i : i) + '】' + d)
                );
                i++;
            }
            $selDt.trigger('change');
        });
    });

    $selDt.change(function () {
        let selOpt = $selDt.children('option:selected').val();
        $.post('search.php', {
            type: 'council',
            county: $selCt.children('option:selected').val(),
            district: selOpt,
        }, function (data) {
            data = JSON.parse(data);
            $selCn.empty();
            if (data && (!Array.isArray(data) || data.length)) {
                for (let id in data) {
                    $selCn.append(
                        $('<option></option>').attr('value', id).html(data[id])
                    );
                }
                $selCn.attr('disabled', false);
                return;
            }
            $selCn.append($('<option value="">目前沒有已知的擬參選人</option>'));
            $selCn.attr('disabled', true);
        });
    });

    $selCt.trigger('change');

})();