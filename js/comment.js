"use strict";
let r = false;

function onRecaptcha() {
    r = true;
    refresh();
}

function refresh() {
    let evVal = $('#ev-cont').val().trim().length;
    let cVal = $('#cont').val().trim().length;
    console.log('evVal: ' + (evVal > 0 ? 'true' : 'false'));
    if (evVal === 0 && cVal === 0) {
        disable('請提供有佐證「或」無佐證的訊息');
        return;
    }
    let email = $('#email').val();
    if (email.length === 0) {
        disable('請輸入電子郵件');
        return;
    }
    if (!validateEmail(email)) {
        disable('電子郵件格式錯誤');
        return;
    }
    if (!r) {
        disable('請完成防止機器人的驗證');
        return;
    }
    enable();
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function disable(text) {
    $('#submit').attr('disabled', true).attr('value', text);
}

function enable() {
    $('#submit').attr('disabled', false).attr('value', '提交留言');
}

$(function () {
    $('#ev-cont').bind('input propertychange', refresh);
    $('#cont').bind('input propertychange', refresh);
    $('#email').bind('input propertychange', refresh);
});