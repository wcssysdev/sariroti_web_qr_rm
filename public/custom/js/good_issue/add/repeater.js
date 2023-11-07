$(document).ready(function () {
    $('.material_list').repeater({
        initEmpty: false,
        defaultValues: {
            'text-input': 'foo'
        },

        show: function () {
            $(this).slideDown(function() {
                $('.material').select2({
                    width: '100%',
                    placeholder: "Material",
                    allowClear: true
                });
                $('.batch').select2({
                    width: '100%',
                    placeholder: "Batch",
                    allowClear: true
                });
                $('.flashsale_detail_promo_disc_type').select2({
                    width: '100%',
                    placeholder: "Promo Type",
                    allowClear: true
                });
                // GET PRODUCT NAME
                // getName();
                // REMOVE FIELD DISC VALUE
                // removeField();
                // AFTER DISC VALUE
                // afterDiscValue();
                // CHECK MAX QTY
                // checkQuantity();
            });
        },

        hide: function (deleteElement) {
            if(confirm('Are you sure you want to delete this element?')) {
                // repeater_limit = repeater_limit - 1;
                $(this).slideUp(deleteElement);
            }
        },

        ready: function (setIndexes) {
            $('.material').select2({
                placeholder: 'Material'
            });
            $('.batch').select2({
                placeholder: 'Batch'
            });
            $('.flashsale_detail_promo_disc_type').select2({
                placeholder: 'Promo Type'
            });
        },
        isFirstItemUndeletable: false
    });
});