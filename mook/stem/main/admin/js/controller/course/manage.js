define(function(require, exports, module) {
	var Notify = require('common/bootstrap-notify');

	exports.run = function(options) {
		var $table = $('#course-table');

		$table.on('click', '.cancel-recommend-course', function(){
			$.post($(this).data('url'), function(html){
				var $tr = $(html);
				$table.find('#' + $tr.attr('id')).replaceWith(html);
				Notify.success('图书推荐已取消！');
			});
		});

		$table.on('click', '.verify-book', function(){
			var title = $(this).attr('title');
			var message = '您确认要' + title + '吗？';
			if (!confirm(message)) return false;
			$.post($(this).data('url'), function(html){
				var $tr = $(html);
				$table.find('#' + $tr.attr('id')).replaceWith(html);
				Notify.success(title + '成功！');
			});
		});

		$table.on('click', '.publish-book', function(){
			var title = $(this).attr('title');
			var message = '您确认要' + title + '吗？';
			if (!confirm(message)) return false;
			$.post($(this).data('url'), function(html){
				var $tr = $(html);
				$table.find('#' + $tr.attr('id')).replaceWith(html);
				Notify.success(title + '成功！');
			});
		});

		$table.on('click', '.delete-book', function() {
			if (!confirm('删除图书，将删除图书的章节、图片信息。真的要删除该图书吗？')) {
				return ;
			}

			var $tr = $(this).parents('tr');
			$.post($(this).data('url'), function(){
				$tr.remove();
			});

		});



	};

});
