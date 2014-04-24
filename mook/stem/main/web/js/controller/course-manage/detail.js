define(function(require, exports, module) {

    // var EditorFactory = require('common/kindeditor-factory');
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    // require('jquery.sortable');

    exports.run = function() {
        require('./header').run();

        // var editor = EditorFactory.create('#course-about-field', 'simple', {extraFileUploadParams:{group:'course'}});

        // var goalDynamicCollection = new DynamicCollection({
        //     element: '#course-goals-form-group',
        // });

        // var audiencesDynamicCollection = new DynamicCollection({
        //     element: '#course-audiences-form-group',
        // });

        // $(".sortable-list").sortable({
        //     'distance':20
        // });

        // $("#course-base-form").on('submit', function() {
        //     goalDynamicCollection.addItem();
        //     audiencesDynamicCollection.addItem();

        // });

        var validator = new Validator({
            element: '#course-detail-form',
            failSilently: true,
            triggerType: 'change'
        });

        validator.addItem({
            element: '[name="wordcount"]',
            rule: 'integer'
        });
    };

});