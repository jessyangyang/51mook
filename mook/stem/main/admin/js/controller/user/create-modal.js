define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

	exports.run = function() {

        var $modal = $('#user-create-form').parents('.modal');
        var validator = new Validator({
            element: '#user-create-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
            	if (error) {
            		return false;
            	}
				$.post($form.attr('action'), $form.serialize(), function(html) {
					$modal.modal('hide');
					Notify.success('添加用户成功');
                    window.location.reload();
				}).error(function(){
					Notify.danger('添加用户失败');
				});

            }
        });
        validator.addItem({
            element: '[name="email"]',
            required: true,
            rule: 'email'
        });

        validator.addItem({
            element: '[name="name"]',
            required: true,
            rule: 'chinese_alphanumeric byte_minlength{min:4} byte_maxlength{max:14}'
        });

        validator.addItem({
            element: '[name="password"]',
            required: true,
            rule: 'minlength{min:5} maxlength{max:20}'
        });

        validator.addItem({
            element: '[name="confirmpassword"]',
            required: true,
            rule: 'confirmation{target:#password}'
        });
	};

});