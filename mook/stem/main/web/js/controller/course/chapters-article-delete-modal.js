define(function(require, exports, module) {
	var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        var articleEditValidator = new Validator({
            element: '#article-delete-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                var $modal = $form.parents('.modal');
                $.post($form.attr('action'), $form.serialize(), function(json) {
                    $modal.modal('hide');
                    Notify.success('删除章节成功！');
                    var ccid = $form.find('.article-id').data('title');
                    $(ccid).remove();
                }).fail(function() {
                    Notify.danger("删除章节失败，请重试！");
                });
            }
        });
    };
});