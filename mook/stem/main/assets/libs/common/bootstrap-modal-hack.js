define(function(require, exports, module) {

    $(function(options) {

        $(document).on('click.data-api', '[data-toggle="modal"]', function(e) {
            var imgUrl=app.config.loading_img_path;
            var $this = $(this);
            href = $this.attr('href');
            url = $this.data('url');
            if (url) {
                var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, '')));
                var $loadingImg = '<div id="box-loading"><div></div></div>';
                $target.html($loadingImg);
                $target.load(url, function(response, status, xhr) {
                    setTimeout(function() {
                        $target.find('.modal').addClass('in').attr({'aria-hidden':'false'});
                    }, 50);
                });
            }
        });

        $('.modal').on('click', '[data-toggle=form-submit]', function(e) {
            e.preventDefault();
            $($(this).data('target')).submit();
        });

        $(".modal").on('click', '.pagination a', function(e){
            e.preventDefault();
            var $modal = $(e.delegateTarget);
            $.get($(this).attr('href'), function(html){
                $modal.html(html);
            });
        });

        $('#search-form input').on('keydown',function(e){
            if(e.keyCode == 13){$(this).parent().submit();}
        });

    });

});