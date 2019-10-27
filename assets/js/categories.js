const $ = require('jquery');
$(document).ready(function(){
    if ($('.tree-grid-item').length){
        $('.tree-grid-item').each(function(){
            $(this).find('.tree-grid-delete').click(function(e){
                e.preventDefault();
                e.stopPropagation();
                if (confirm('Are you sure you want to remove this category of categories?')) {
                    window.location.href = $(this).attr('data-browse');
                }
            });
            $(this).find('.tree-grid-add').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                var form = duplicateForm();
                form.appendTo($(this).parents('.tree-grid-item').first()).find('#category_parent').val($(this).attr('data-target'));
            });
        });
        $('.tree-grid-item-folder-container').click(function(){
            $(this).parents('li').first().toggleClass('tree-grid-item-opened');
            $(this).children().first().toggleClass('fa-folder fa-folder-open');
        });

        $('.tree-grid-item').hover(
            function(e){
                e.stopPropagation();
                $(this).parents('.tree-grid-item').removeClass('tree-grid-active');
                $(this).addClass('tree-grid-active');
            },
            function(e){
                e.stopPropagation();
                $(this).parents('.tree-grid-item').first().addClass('tree-grid-active');
                $(this).removeClass('tree-grid-active');
            });

        $('.tree-grid-edit').click(function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var li = $(this).parents('li').first();
            var wrapper = li.find('.tree-grid-item-wrapper').first();
            duplicateForm()
                .insertAfter(wrapper)
                .find('form')
                .find('.create-form-button').hide()
                .end()
                .find('.hidden').removeClass('hidden')
                .end()
                .attr('action', $(this).attr('data-browse'))
                .addClass('edit-form')
                .find('#category_parent').val(
                $(this).attr('data-target')
            ).end()
                .find('.create-form-cancel-button').click(function(){
                $(this).parents('li').first().find('.tree-grid-item-wrapper').show();
                $(this).parents('.tree-grid-create').remove();
            }).end()
                .find('#category_name').val(
                li.find('.tree-grid-item-name').first().text().trim()
            ).focus();
            wrapper.hide();
        });
    }
});

function duplicateForm() {
    $('.tree-grid .tree-grid-create').remove();
    return $('.tree-grid-create').clone();
}