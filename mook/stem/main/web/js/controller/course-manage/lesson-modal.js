define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

    require('redactor/redactor.css');
    require('redactor/redactor.min.js');
    require('redactor/lang/zh_cn.js');

    exports.run = function() {
        require('./header').run();

        $('#lesson-content-field').redactor({
            lang: 'zh_cn',
            iframe: true,
            minHeight: 400,
            maxHeight: 650,
            autoresize: true,
            css: '/assets/libs/redactor/css/custom.css',
            imageUpload: $("[name='image_upload']").val(),
            imageGetJson: $("[name='image_list']").val()
        });
        
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