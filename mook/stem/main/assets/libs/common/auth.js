define(function(require, exports, module) {
    $(function(options) {

        $('.accounts').on('click',function(e){
            var that = $(this);
            var body = $('body');
            if ($('#login-form,#register-form').length > 0)return false;
            $.post(that.data('url'),{'account':'1'},function(response){

                var $html = $(response);
                $html.appendTo('body');
                body.append('<div class="modal-backdrop fade"></div>');
                body.attr('class','modal-open');
                setTimeout(function() {
                    $html.find('.modal').addClass('in').attr({'aria-hidden':'false'});
                    $('.modal-backdrop').addClass('in');
                }, 50);

                $('#id_login,#id_register').on('click',function(e){
                    var $drag = $(this);
                    var gel = $drag[0];
                    if (gel == e.target) {

                        $('.modal-backdrop').removeClass('in');
                        $drag.removeClass('in');
                        setTimeout(function() {
                            $drag.parent().remove();
                            $('.modal-backdrop').remove();
                            body.attr('class','');
                        }, 200);
                    }
                });
            });
        });

    });
});