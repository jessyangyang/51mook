define(function(require, exports, module) {

	var Notify = require('common/bootstrap-notify');

	exports.run = function() {

		var $table = $('#group-table');

		$table.on('click', '#user-groups-delete', function() {
            var $trigger = $(this);

            if (!confirm('真的要' + $trigger.attr('title') + '吗？')) {
                return ;
            }

            $.post($(this).data('url'), function(html){
                Notify.success($trigger.attr('title') + '成功！');
                var $tr = $(html);
                console.log(html);
                $('#' + $tr.attr('id')).replaceWith($tr);
                window.location.reload();
            }).error(function(){
                Notify.danger($trigger.attr('title') + '失败');
            });
        });

	};

});