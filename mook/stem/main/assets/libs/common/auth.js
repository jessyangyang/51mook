define(function(require, exports, module) {
    $(function(options) {

        $('.accounts').on('click',function(){
            var that = $(this);
            var body = $('body');
            body.attr('class','modal-open');
            $.post(that.data('url'),{'account':'1'},function(response){
                body.append(response);
                body.append('<div class="modal-backdrop fade in"></div>');

                $('#id_login,#id_register').on('click',function(e){
                    var drag = $(this);
                    var gel = drag[0];
                    if (gel == e.target) {
                        $('#login-form,#register-form').fadeOut('slow').remove();
                        $('.modal-backdrop').remove();
                        body.attr('class','');
                    }
                });
            });
        });

    });
});