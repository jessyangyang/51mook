define(function(require, exports, module) {
	var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {

    	$('#course-action-delete').on('click',function(){
    		var $modal = $('.modal');
    		var $this = $(this);
            var href = $this.attr('href');
            var url = $this.data('url');
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
                var $button = $('.m-modal-dialog').find('button');
                $button.addClass('loading');
                var formdata = new FormData($form[0]);

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data:  formdata,
                    mimeType:"multipart/form-data",
                    contentType: false,
                    processData:false
                }).done(function(data, textStatus, jqXHR) {
                    if (data) {
                        var json = jQuery.parseJSON(data);
                        $('#course-title-show').text(json.message.title);
                        $('#course-category-show').text(json.message.category);
                    }
                    $button.removeClass('loading');
                    $modal.modal('hide');
                    Notify.success('修改课程成功！');
                }).fail(function(jqXHR, textStatus, errorThrown) { 
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