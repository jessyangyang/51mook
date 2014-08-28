define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);
    require('jquery.sortable');

    exports.run = function() {
        _sortableChapterAction();
        _linkAction();
    };

    function _sortableChapterAction(){
        var adjustment;

        var $list = $(".m-catalog").sortable({
            itemSelector:"li",
            distance: 10,
            handle: 'span.m-move',
            onDragStart: function($item, container, _super) {
                $item.attr('class','ui-sortable-helper');

                var offset = $item.offset();
                pointer = container.rootGroup.pointer;

                adjustment = {
                    left: pointer.left - offset.left,
                    top: pointer.top - offset.top
                };


                _super($item, container);
            },
            onDrag: function ($item, position) {
                $item.css({
                    left: position.left - adjustment.left,
                    top: position.top - adjustment.top
                });
            },
            onDrop: function (item, container, _super) {
                _super(item, container);
                var data = $list.sortable("serialize").get();
                item.removeAttr('class');
                $.post($list.data('sortUrl'), {ids:data}, function(response){
                    // $list.find('.item-chapter').each(function(index){
                    //     $(this).find('.chapter-sort').text(index+1);
                    // });
                    
                });
            },
            serialize: function(parent, children, isContainer) {
                return isContainer ? children : parent.attr('id');
            }
        });
    }

    function _linkAction(){
        $('[name="_link"]').focus(function(){
            $('.point').hide();
            $("#add-form label").animate({width: "0px"}, 150);
            $('#add-form textarea').animate({height:'73px'},200,function(){
                    $('#add-form').attr('class','open');
            });
        });

        $('#link-cancel').click(function(){
            $('#add-form textarea').animate({height:'0px'},200);
            $("#add-form label").animate({
                    width: "70px"
                }, 150,function(){
                    $('.point').show();
                    $('#add-form').removeAttr('class','open');
                });
        });

        var addValidator = new Validator({
            element: '#add-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                $('span.error').hide();

                var message = '';
                for (var i = results.length - 1; i >= 0; i--) {
                    if (results[i][1]) { message = results[i][1];}
                }
                if (error) {
                    $('span.error').text(message).attr('style','display:block;');
                    return ;
                }
                $form.find('button').attr('class', 'btn loading disabled');
                $('ul.m-catalog').append('<li class="loader"><span class="m-dot"><img alt="loading" src="/web/img/default/loader.gif"></span><h2>获取链接地址中...</h2></li>');

                $.post($form.attr('action'), $form.serialize(), function(response) {
                    $form.find('button').attr('class', 'btn');
                    $('li.loader').remove();
                    $('li.empty').remove();
                    $('ul.m-catalog').append(response);
                    $form.find('#course-link').val('');
                    $form.find('#course-link-summary').val('');
                }).error(function(jqxhr, textStatus, errorThrown){
                    var json = jQuery.parseJSON(jqxhr.responseText);
                    $('span.error').text(json.message.error).attr('style','display:block;');

                    $form.find('button').attr('class', 'btn');
                    $('li.loader').remove();
                    $form.find('#course-link').val('');
                    $form.find('#course-link-summary').val('');
                });

            }
        });

        addValidator.addItem({
            element: '[name="_link"]',
            required: true,
            rule : 'url'
        });

        addValidator.addItem({
            element: '[name="_summary"]',
            required: true,
            errormessageRequired: '为你的课程添加简介，让学员了解您的核心内容',
            rule: 'minlength{min:5} maxlength{max:200}'
        });
    }

});