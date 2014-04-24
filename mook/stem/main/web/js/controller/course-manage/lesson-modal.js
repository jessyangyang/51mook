define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require('ckeditor/ckeditor');

    exports.run = function() {
        require('./header').run();

        CKEDITOR.replace( 'lesson-content-field' );

        var validator = new Validator({
            element: '#course-content-form',
            triggerType: 'change'
        });

        validator.addItem({
            element: '[name="title"]',
            required: true
        });
    }
});