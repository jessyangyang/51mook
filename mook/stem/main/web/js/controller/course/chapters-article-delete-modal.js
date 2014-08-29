define(function(require, exports, module) {
	var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        var articleEditValidator = new Validator({
            element: '#article-delete-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                var $modal = $form.parents('.modal');
                var $button = $('.m-modal-dialog').find('button');
                $.post($form.attr('action'), $form.serialize(), function(json) {
                    var ccid = $form.find('.article-id').data('title');
                    $(ccid).remove();
                    $button.removeClass('loading');
                    $modal.modal('hide');
                    Notify.success('删除章节成功！');
                }).fail(function() {
                    Notify.danger("删除章节失败，请重试！");
                });
            }
        });
    };
});