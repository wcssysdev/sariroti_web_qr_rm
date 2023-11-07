// Class definition

var KTBootstrapDatepicker = function () {

    var arrows;
    if (KTUtil.isRTL()) {
        arrows = {
            leftArrow: '<i class="la la-angle-right"></i>',
            rightArrow: '<i class="la la-angle-left"></i>'
        }
    } else {
        arrows = {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        }
    }
    
    // Private functions
    var demos = function () {
        // minimum setup
        $('.date').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            format: "dd-mm-yyyy",
            orientation: "bottom left",
            templates: arrows,
            autoclose: true,
            todayBtn: "linked",
            clearBtn: true
        });

        // Datetime Picker
        $('.datetime').datetimepicker({
            rtl: KTUtil.isRTL(),
            // todayHighlight: true,
            // format: "yyyy-mm-dd",
            orientation: "bottom left",
            templates: arrows,
            autoclose: true,
            todayBtn: "linked",
            clearBtn: true,
            locale: 'de'
        });
        
    }

    return {
        // public functions
        init: function() {
            demos(); 
        }
    };
}();

jQuery(document).ready(function() {    
    KTBootstrapDatepicker.init();
});