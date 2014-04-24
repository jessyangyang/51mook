define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

	exports.run = function() {

        var $modal = $('#group-edit-form').parents('.modal');

        var validator = new Validator({
            element: '#group-edit-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
            	if (error) {
            		return false;
            	}

				$.post($form.attr('action'), $form.serialize(), function(html) {
                    $modal.modal('hide');
                    Notify.success('用户组修改成功');
                    var $tr = $(html);
                    console.log(html);
					$('#' + $tr.attr('id')).replaceWith($tr);
				}).error(function(){
					Notify.danger('操作失败');
				});
            }
        });

        validator.addItem({
            element: '[name="groupname"]',
            required: true,
            rule: 'chinese_alphanumeric byte_minlength{min:2} byte_maxlength{max:14}'
        });

	};

});