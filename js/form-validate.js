"use strict";
$(function () {

    let spaceReg = new RegExp('\\s+');
    function isEmpty($ta) {
        return $ta.val().replace(spaceReg, "").length === 0;
    }

    let flag = false;
    $('#submit').click(function () {
        if (flag) {
            return false;
        }
        if (isEmpty($('#cont')) && isEmpty($('#ev_cont'))) {
            $('#errm').html("訊息內容不得為空。請至少提供已佐證訊息或未佐證訊息其中之一。");
            return false;
        }
        if (isEmpty($('#au_email'))) {
            $('#errm').html("請輸入您的信箱。");
            return false;
        }
        return (flag = true);
    });

});