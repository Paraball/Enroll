"use strict";

(function () {

    let $selCt = $('#county');
    let $selDt = $('#district');
    let $selCn = $('#candidate');

    $selCt.change(function () {
        let val = $selCt.val();
        console.log(val);
        if (!val) {
            $selDt.empty().attr('disabled', true).append('<option value="">所有選區</option>');
            $selCn.empty().attr('disabled', true).append('<option value="">所有擬參選人</option>')
            return;
        }
        $.get('search.php', {
            type: 'council',
            county: val,
        }, function (data) {
            data = JSON.parse(data);
            let i = 1;
            $selDt.empty().append('<option value="">所有選區</option>');
            for (let d of data) {
                $selDt.append(
                    $('<option></option>').attr('value', i).html('【' + (i < 10 ? '0' + i : i) + '】' + d['district_name'])
                );
                i++;
            }
            $selDt.attr('disabled', false).trigger('change');
        });
    });

    $selDt.change(function () {
        let dVal = $selDt.val();
        if (!dVal) {
            $selCn.empty().attr('disabled', true).append('<option value="">所有擬參選人</option>');
            return;
        }
        $.get('search.php', {
            type: 'council',
            county: $selCt.val(),
            district: dVal,
        }, function (data) {
            data = JSON.parse(data);
            $selCn.empty().append('<option value="">所有擬參選人</option>');
            if (data && (!Array.isArray(data) || data.length)) {
                for (let d of data) {
                    $selCn.append(
                        $('<option></option>').attr('value', d['candidate_id']).html(d['candidate_name'])
                    );
                }
                $selCn.attr('disabled', false);
                return;
            }
            $selCn.attr('disabled', true).html('<option>尚無擬參選人</option>');
        });
    });

})();