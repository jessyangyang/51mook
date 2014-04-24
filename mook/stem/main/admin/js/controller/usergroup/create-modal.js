define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

	exports.run = function() {

        var $modal = $('#group-create-form').parents('.modal');
        var validator = new Validator({
            element: '#group-create-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
            	if (error) {
            		return false;
            	}
				$.post($form.attr('action'), $form.serialize(), function(html) {
					$modal.modal('hide');
					Notify.success('添加用户组成功');
                    window.location.reload();
				}).error(function(){
					Notify.danger('添加用户组失败');
				});

            }
        });
        validator.addItem({
            element: '[name="id"]',
            required: true,
            rule: 'integer'
        });

        validator.addItem({
            element: '[name="groupname"]',
            required: true,
            rule: 'chinese_alphanumeric byte_minlength{min:2} byte_maxlength{max:14}'
        });
	};

});