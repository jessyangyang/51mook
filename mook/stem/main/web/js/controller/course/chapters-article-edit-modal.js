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
                var $button = $('.m-modal-dialog').find('button');
                $button.addClass('loading');
                $.post($form.attr('action'), $form.serialize(), function(json) {
                    $button.removeClass('loading');
                    $modal.modal('hide');

                    var $item = $($form.data('item'));
                    if (json) {
                        $item.find('h2 a').text(json.message.title);
                    }
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