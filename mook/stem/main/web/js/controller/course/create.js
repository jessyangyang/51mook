define(function(require, exports, module) {

	var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {
        var validator = new Validator({
            element: '#course-create-form',
            triggerType: 'change'
        });

        validator.addItem({
            element: '[name="title"]',
            required: true,
            rule: 'minlength{min:4}'
        });

        validator.addItem({
            element: '[name="description"]',
            required: true,
            rule: 'minlength{min:10}'
        });

        validator.addItem({
            element: '[name="tags"]',
            required: true
        });

        validator.addItem({
            element: '[name="roles"]',
            required: true
        });
    };

});