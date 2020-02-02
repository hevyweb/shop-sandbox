$(document).ready(function(){
    $('.subtract-one').click(function(e){
        e.preventDefault();
        changeValue(this, true)
    });

    $('.add-one').click(function(e){
        e.preventDefault();
        changeValue(this, false)
    });

    $('.submit-button').click(function(e){
        e.preventDefault();
        $(this).parents('tr').find('form').submit();
    });

    $('.order-delete').click(function(e){
        if (!confirm($(this).attr('data-content'))){
            e.preventDefault();
        }
    })
});

function changeValue(button, down) {
    var input = $(button).parents('td').find('input');
    var value = down ? parseInt(input.val()) -1 : parseInt(input.val()) + 1;
    if (value > 0) {
        input.val(value);
    }
}