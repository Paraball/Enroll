$(function () {

    let flag = false;

    $('#submit').click(function () {
        if(flag){
            return false;
        }
        if ($('#content').val() && $('#author').val()) {
            flag = true;
            return true;
        }
        $('#errm').html("訊息內容或暱稱不得為空。");
        return false;
    });

});