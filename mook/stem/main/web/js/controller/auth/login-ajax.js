define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {
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
    };

});