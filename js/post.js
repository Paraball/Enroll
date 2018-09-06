'use strict';
$(function () {
    $('.links a.edit').each(function () {
        $(this).attr('href', 'edit.php?post_id=' + $(this).attr('post-id') + '&edit=1');
    })
    $('.links a.report').click(function () {
        //TODO
    });

    $('.links a.delete').one('click', function () {
        let $this = $(this);
        let postId = $this.attr('post-id');
        $.get(
            'edit.php?status=-1&post_id=' + postId,
            function (data) {
                if (data === '1') {
                    $this.parents('.board').slideUp(400);
                }
            }
        );
        return false;
    });
    $('.links a.publish').one('click', function () {
        let $this = $(this);
        let postId = $this.attr('post-id');
        $.get(
            'edit.php?status=1&post_id=' + postId,
            function (data) {
                if (data === '1') {
                    $this.parents('.board').slideUp(400);
                }
            }
        );
        return false;
    });
    $('.links a.cancel').one('click', function () {
        let $this = $(this);
        let postId = $this.attr('post-id');
        $.get(
            'edit.php?status=0&post_id=' + postId,
            function (data) {
                if (data === '1') {
                    $this.parents('.board').slideUp(400);
                }
            }
        );
        return false;
    });
    $('.links a.die').one('click', function () {
        let $this = $(this);
        let postId = $this.attr('post-id');
        $.get(
            'edit.php?status=-2&post_id=' + postId,
            function (data) {
                if (data === '1') {
                    $this.parents('.board').slideUp(400);
                }
            }
        );
        return false;
    });
});