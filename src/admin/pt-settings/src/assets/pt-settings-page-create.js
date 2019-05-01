jQuery(document).ready(function( $ ){

    var ajax_url = PT_Settings.ajaxurl;

    $( 'a.pt-settings-create-page-button' ).click( function(){
        var $this = $(this);

        $.post( ajax_url, {
            action: $this.data('action'),
            _wpnonce: $this.data('nonce'),
            key: $this.data('key')
        }, function( resp ) {
            if ( resp.success ) {
                var page_id = resp.data.page_id;
                var page_title = resp.data.page_title;
                var $select = $this.prev('select');
                $select.append('<option value="'+page_id+'" selected="selected">'+page_title+'</option>' );
                $this.replaceWith( resp.data.link );
            } else {

            }
            $this.next('.pt-settings-create-page-status').html(resp.data.message);
        });

        return false;
    });

});
