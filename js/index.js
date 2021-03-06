"use strict";
(function () {
    let $selCt = $('#county');
    let $selDt = $('#district');
    let $selCn = $('#cccd');
    let $submit = $('#submit');

    $selCt.change(function () {
        let selOpt = $selCt.val();
        console.log(selOpt + " is selected.");
        $selDt.empty();
        $.get('search.php', {
            type: 'council',
            county: selOpt,
        }, function (data) {
            data = JSON.parse(data);
            let i = 1;
            for (let d of data) {
                $selDt.append(
                    $('<option></option>').attr('value', i).html('【' + (i < 10 ? '0' + i : i) + '】' + d['district_name'])
                );
                i++;
            }
            $selDt.trigger('change');
        });
    });

    $selDt.change(function () {
        let selOpt = $selDt.val();
        $.get('search.php', {
            type: 'council',
            county: $selCt.val(),
            district: selOpt,
        }, function (data) {
            data = JSON.parse(data);
            $selCn.empty();
            if (data && (!Array.isArray(data) || data.length)) {
                for (let d of data) {
                    $selCn.append(
                        $('<option></option>').attr('value', d['candidate_id']).html(d['candidate_name'])
                    );
                }
                $selCn.attr('disabled', false);
                $submit.attr('disabled', false);
                return;
            }
            $selCn.attr('disabled', true);
            $selCn.append($('<option>目前沒有已知的擬參選人</option>'));
            $submit.attr('disabled', true);
        });
    });

    $submit.click(function () {
        $.cookie('county', $selCt.val());
        $.cookie('district', $selDt.val());
        $.cookie('candidate', $selCn.val());
        return true;
    });

})();