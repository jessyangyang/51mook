define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

    // require('ckeditor/ckeditor');
    require('redactor/redactor.css');
    require('redactor/redactor.min.js');
    require('redactor/lang/zh_cn.js');

    exports.run = function() {
        require('./header').run();

        $('#lesson-content-field').redactor({
            lang: 'zh_cn',
            imageUpload: $("[name='image_upload']").val(),
            imageGetJson: $("[name='image_list']").val()
        });
        // CKEDITOR.replace( 'lesson-content-field' );
        console.log($.Redactor.opts);
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