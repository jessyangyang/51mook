define(function(require, exports, module) {

    require('jquery.sortable');
    var Sticky = require('sticky');
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        require('./header').run();

        var $list = $("#book-menu-table tbody").sortable({
            itemSelector:"tr",
            distance: 20,
            onDrop: function (item, container, _super) {
                _super(item, container);
                var data = $list.sortable("serialize").get();
                $.post($list.data('sortUrl'), {ids:data}, function(response){
                    $list.find('.item-chapter').each(function(index){
                        $(this).find('.chapter-sort').text(index+1);
                    });
                    
                });
            },
            serialize: function(parent, children, isContainer) {
                return isContainer ? children : parent.attr('id');
            }
        });

        $("#book-menu-table").on('click', '.delete-menu', function(e) {
            if (!confirm('您真的要删除该章节吗？')) {
                return ;
            }
            var $btn = $(e.currentTarget);
            $.post($(this).data('url'), function(html) {
                $btn.parents('.item-chapter').remove();
                Notify.success('章节已删除！');
            });
        });

        $("#course-item-list").on('click', '.publish-lesson-btn', function(e) {
            var $btn = $(e.currentTarget);
            $.post($(this).data('url'), function(html) {
                var id = '#' + $(html).attr('id');
                $(id).replaceWith(html);
                $(id).find('.btn-link').tooltip();
                Notify.success('图书发布成功！');
            });
        });

        $("#course-item-list").on('click', '.unpublish-lesson-btn', function(e) {
            var $btn = $(e.currentTarget);
            $.post($(this).data('url'), function(html) {
                var id = '#' + $(html).attr('id');
                $(id).replaceWith(html);
                $(id).find('.btn-link').tooltip();
                Notify.success('图书已取消发布！');
            });
        });

        Sticky('.lesson-manage-panel .panel-heading', 0, function(status){
            if (status) {
                var $elem = this.elem;
                $elem.addClass('sticky');
                $elem.width($elem.parent().width() - 10);
            } else {
                this.elem.removeClass('sticky');
                this.elem.width('auto');
            }
        });

        $("#course-item-list .item-actions .btn-link").tooltip();

    };

});