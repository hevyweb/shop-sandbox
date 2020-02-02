$(document).ready(function(){
    $('.delete-action').each(function(){
        $(this).click(function(e){
            if ($(this).attr('data-content')){
                if (!confirm($(this).attr('data-content'))){
                    e.preventDefault();
                }
            }
        })
    });

    $('#delete-all-products').click(function(){
        if (confirm($(this).attr('data-content'))){
            $('.delete-all-products-form').submit();
        }
    });

    $('.check-all').click(function(){
        $(this).parents('table').find('tbody input[type=checkbox]').prop('checked', $(this).is(':checked'));
    });
});