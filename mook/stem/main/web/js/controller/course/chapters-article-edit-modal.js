define(function(require, exports, module) {
	var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {
        var articleEditValidator = new Validator({
            element: '#article-edit-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                var $modal = $form.parents('.modal');

                $.post($form.attr('action'), $form.serialize(), function(json) {
                    $modal.modal('hide');
                    Notify.success('修改章节成功！');
                }).fail(function() {
                    Notify.danger("修改章节失败，请重试！");
                });
            }
        });

        articleEditValidator.addItem({
            element: '[name="title"]',
            required: true
        });

        articleEditValidator.addItem({
            element: '[name="summary"]',
            required: true,
            rule: 'minlength{min:5} maxlength{max:200}'
        });
    };
});