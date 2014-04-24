define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');

    exports.run = function() {
        var validator = new Validator({
            element: '#login-form'
        });

        validator.addItem({
            element: '[name="email"]',
            required: true,
            rule: 'email'
        });

        validator.addItem({
            element: '[name="password"]',
            required: true,
            rule: 'minlength{min:5} maxlength{max:20}'
        });

    };

});