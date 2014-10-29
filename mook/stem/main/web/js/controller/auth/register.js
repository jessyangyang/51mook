define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {
        var validator = new Validator({
            element: '#register-form'
        });

        validator.addItem({
            element: '[name="email"]',
            required: true,
            rule: 'email'
        });

        validator.addItem({
            element: '[name="password"]',
            required: true,
            rule: 'minlength{min:5} maxlength{max:50}'
        });

        validator.addItem({
            element: '[name="confirmpassword"]',
            required: true,
            rule: 'confirmation{target:#id_password}'
        });

        validator.addItem({
            element: '#id_tos',
            required: true,
            errormessageRequired: '勾选同意此服务协议，才能继续注册'
        });

    };

});