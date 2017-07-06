$(document).ready(function(){

    $('#appbundle_product_category').select2({
        'placeholder': 'Catégorie'
    });

    $('form[name=appbundle_product]').submit(function(){
        var $form = $(this);
        var $container = $('#products');

        var list = $('#listId').val();
        var name = $('#appbundle_product_name').val();
        var category = $('#appbundle_product_category').val();

        $.ajax({
            url: Routing.generate('lists_add_new_product', {'list': list}),
            type: 'POST',
            data: {productName: name, productCategory: category},
            success: function(data){
                // We remove the default message in the list if it's still there
                var $default = $container.find('.default');
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

});