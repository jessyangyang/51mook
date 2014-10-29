define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {

        $('.accounts').on('click',function(e){
            var that = $(this);
            var body = $('body');
            if ($('#login-body,#register-body').length > 0) return false;
            $.post(that.data('url'),{'account':'1'},function(response){

                var $html = $(response);
                $html.appendTo('body');
                body.append('<div class="modal-backdrop fade"></div>');
                body.attr('class','modal-open');
                setTimeout(function() {
                    $html.find('.modal').addClass('in').attr({'aria-hidden':'false'});
                    $('.modal-backdrop').addClass('in');
                }, 50);

                // _loginCheck();

                $('#login-body,#register-body').on('click',function(e){
                    var $drag = $(this);
                    var gel = $drag[0];
                    if (gel == e.target) {

                        $('.modal-backdrop').removeClass('in');
                        $drag.removeClass('in');
                        setTimeout(function() {
                            $drag.parent().remove();
                            $('.modal-backdrop').remove();
                            body.attr('class','');
                        }, 200);
                    }
                });
            });
        });

        function _loginCheck()
        {
            var loginValidator = new Validator({
            element: '#login-action-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                $form.find('.m-error').hide();

                if (error) {
                    return ;
                }

                $.post($form.attr('action'), $form.serialize(), function(json) {
                    if (json.success == 1) {
                        window.location.reload();
                    }
                    else
                    {
                        $form.find('.m-error').html(json.message).attr('style','display:block;');
                    }
                }, 'json').error(function(jqxhr, textStatus, errorThrown) {
                    var json = jQuery.parseJSON(jqxhr.responseText);
                    $form.find('.m-error').html(json.message).show();
                });
                }
            });

            loginValidator.addItem({
                element: '[name="email"]',
                required: true,
                rule: 'email'
            });

            loginValidator.addItem({
                element: '[name="password"]',
                required: true,
                rule: 'minlength{min:5} maxlength{max:20}'
            });
        }
    };
});