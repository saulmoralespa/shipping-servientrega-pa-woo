(function($){
    const $button = 'button.shipping-servientrega-status-to-processing';
    const action = 'shipping_servientrega_order_status_changed_state_guide';

    const messages = {
        [action]: {
            title: 'Agendando actualización',
            successText: 'La actualización de ordenes se ha agendado correctamente'
        }
    }

    $($button).click(function (e) {
        const self = $(this);

        $.ajax({
            data: {
                action,
                nonce: $(this).data("nonce")
            },
            type: 'POST',
            url: ajaxurl,
            dataType: "json",
            beforeSend : () => {
                Swal.fire({
                    title: messages[action].title,
                    didOpen: () => {
                        Swal.showLoading()
                    },
                    allowOutsideClick: false
                });
            },
            success: (r) => {
                if (r.status){
                    Swal.fire({
                        icon: 'success',
                        text: messages[action].successText,
                        allowOutsideClick: false,
                        showCloseButton: true,
                        showConfirmButton: false
                    })
                }else{
                    Swal.fire(
                        'Error',
                        r.message ?? 'Ha ocurrido un error inesperado',
                        'error'
                    );
                }
            }
        });
    });
})(jQuery);