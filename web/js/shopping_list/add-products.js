$(document).ready(function(){

    $('#appbundle_product_category').select2({
        'placeholder': 'Catégorie'
    });

    var list = $('#listId').val();
    var $container = $('#products');
    var $default = $container.find('.default');

    $('form[name=appbundle_product]').submit(function(){
        var $form = $(this);

        var name = $('#appbundle_product_name').val();
        var category = $('#appbundle_product_category').val();

        $.ajax({
            url: Routing.generate('lists_add_new_product', {'list': list}),
            type: 'POST',
            data: {productName: name, productCategory: category},
            success: function(data){
                // We remove the default message in the list if it's still there
                if ( $default.length > 0 ) {
                    $default.remove();
                }

                // We add the product to the list
                $container.prepend(data);

                // We clear the product form
                $form.reset();
            }
        });

        return false;
    });

    $('.addExistingProduct').click(function(){
        var $this = $(this);
        var product = $this.attr('data-id');

        $.ajax({
            url: Routing.generate('lists_add_existing_product', {'list': list}),
            type: 'POST',
            data: {product: product},
            success: function(data){
                // We remove the default message in the list if it's still there
                if ( $default.length > 0 ) {
                    $default.remove();
                }

                // We add the product to the list
                $container.prepend(data);

                // We remove the product so we can't add it again and throw an error
                $this.remove();
            }
        });
    });

    $('.list-item .fa-remove').click(function(){
        var $item = $(this).parent();
        var product = $item.attr('data-id');

        var category = $item.attr('data-category');
        var $container = $('#category-' + category + ' .list-content ul');

        $.ajax({
            url: Routing.generate('lists_remove_product', {'list': list}),
            type: 'POST',
            data: {product: product},
            success: function(data){
                // We add back the product to its category list
                $container.prepend(data);

                // We remove the product so we can't add it again and throw an error
                $item.remove();
            }
        });
    });

});