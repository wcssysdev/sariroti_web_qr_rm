jQuery(document).ready(function() {
    $('.max_length_input').maxlength({
        threshold: 3,
        warningClass: "label label-success label-rounded label-inline",
        limitReachedClass: "label label-warning label-rounded label-inline",
        separator: ' dari ',
        postText: ' karakter tersisa.',
        validate: true
    });
});