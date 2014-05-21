define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

	exports.run = function() {

        var $modal = $('#collection-blog-form').parents('.modal');
        var validator = new Validator({
            element: '#collection-blog-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
            	if (error) {
            		return false;
            	}
				$.post($form.attr('action'), $form.serialize(), function(html) {
					$modal.modal('hide');
					Notify.success('采集Blog成功');
                    // window.location.reload();
				}).error(function(){
					Notify.danger('采集Blog失败');
				});

            }
        });

        validator.addItem({
            element: '[name="url"]',
            rule: 'url'
        });

        validator.addItem({
            element: '[name="year"]',
            required: true
        });

        validator.addItem({
            element: '[name="page"]',
            required: true
        });

	};

});