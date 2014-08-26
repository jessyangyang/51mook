define(function(require, exports, module) {
	var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {

    	$('#course-action-delete').on('click',function(){
    		var $modal = $('.modal');
    		var $this = $(this);
            href = $this.attr('href');
            url = $this.data('url');
            // $('.modal-backdrop').remove();
            // $modal.modal('hide');
            if (url) {
                var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, '')));
                $target.html('').load(url, function(response, status, xhr) {
                    setTimeout(function() {
                        $target.find('.modal').addClass('in').attr({'aria-hidden':'false'});
                    }, 50);
                });
            }

    	});

        var courseEditValidator = new Validator({
            element: '#course-edit-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                var $modal = $form.parents('.modal');

                $.post($form.attr('action'), $form.serialize(), function(json) {
                    $modal.modal('hide');
                    Notify.success('修改课程成功！');
                }).fail(function() {
                    Notify.danger("修改课程失败，请重试！");
                });
            }
        });

        courseEditValidator.addItem({
            element: '[name="title"]',
            required: true
        });

        courseEditValidator.addItem({
            element: '[name="summary"]',
            required: true,
            rule: 'minlength{min:5} maxlength{max:200}'
        });
    };
});