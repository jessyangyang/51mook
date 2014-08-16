define(function(require, exports, module) {

    require('jquery.sortable');
    var Validator = require('bootstrap.validator');

    exports.run = function() {

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
                item.removeAttr('class');
                var data = $list.sortable("serialize").get();
                // $.post($list.data('sortUrl'), {ids:data}, function(response){
                //     $list.find('.item-chapter').each(function(index){
                //         $(this).find('.chapter-sort').text(index+1);
                //     });
                    
                // });
            },
            serialize: function(parent, children, isContainer) {
                return isContainer ? children : parent.attr('id');
            }
        });

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

        var validator = new Validator({
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
                $('ul.m-catalog').append('<li class="loader"><span class="m-dot"><img alt="loading" src="/web/img/default/loading.gif"></span><h2>获取链接地址中...</h2></li>');

                $.post($form.attr('action'), $form.serialize(), function(response) {
                    $form.find('button').attr('class', 'btn');
                    var data = response.message.content;
                    if (data) {
                        $('li.loader').remove();
                        $('li.empty').remove();
                    
                        var html = '<li><span class="m-dot"><span class="icon-m-article-cata"></span></span><span class="m-move"><span class="icon-m-envelope"></span></span><h2><a href="/course/' + data.cid + '/' + data.ccid + '/' + data.ptitle + '">' + data.title + '</a></h2><p class="small m-meta"><a href="' + data.url +'">' + data.host +'</a><span> • </span><a href="#">' + data.student + ' 学生</a><span> • </span><a href="#">' + data.studytime + ' 分钟</a></p></li>';

                        $('ul.m-catalog').append(html);
                    }

                },'json').error(function(jqxhr, textStatus, errorThrown){
                    var json = jQuery.parseJSON(jqxhr.responseText);
                    $('span.error').text(json.message.error).attr('style','display:block;');

                    $form.find('button').attr('class', 'btn');
                    $('li.loader').remove();
                });

            }
        });

        validator.addItem({
            element: '[name="_link"]',
            required: true,
            rule : 'url'
        });

        validator.addItem({
            element: '[name="_summary"]',
            required: true,
            rule: 'minlength{min:5} maxlength{max:100}'
        });




    };

});