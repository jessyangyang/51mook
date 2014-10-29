define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {
        var registerValidator = new Validator({
            element: '#register-action-form',
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
            }});

            registerValidator.addItem({
                element: '[name="email"]',
                required: true,
                rule: 'email'
            });

            registerValidator.addItem({
                element: '[name="password"]',
                required: true,
                rule: 'minlength{min:5} maxlength{max:20}'
            });

            registerValidator.addItem({
                element: '[name="confirmpassword"]',
                required: true,
                rule: 'confirmation{target:#id_password}'
            });

            registerValidator.addItem({
                element: '#id_tos',
                required: true,
                errormessageRequired: '勾选同意此服务协议，才能继续注册'
            });
    };

});