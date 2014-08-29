define(function(require, exports, module) {
	var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        var articleEditValidator = new Validator({
            element: '#course-delete-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                var $modal = $form.parents('.modal');
                var $button = $('.m-modal-dialog').find('button');
                $button.addClass('loading');
                $.post($form.attr('action'), $form.serialize(), function(json) {
                    $button.removeClass('loading');
                    $modal.modal('hide');
                    Notify.success('删除课程成功！');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                }).fail(function() {
                    Notify.danger("删除课程失败，请重试！");
                });
            }
        });
    };
});