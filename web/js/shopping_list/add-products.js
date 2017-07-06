$(document).ready(function(){

    $('#appbundle_product_category, #categoryFilter').select2({
        placeholder: 'Catégorie',
        minimumResultsForSearch: Infinity
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

    $('ul[id^=category-], #search-results').on('click', '.list-item .fa-plus', function(){
        var $item= $(this).parent();
        var product = $item.attr('data-id');

        $.ajax({
            url: Routing.generate('lists_add_existing_product', {'list': list}),
            type: 'POST',
            data: {product: product},
            success: function(data){
                // We remove the default message in the list if it's still there
                if ( $default.length > 0 ) {
                    $default.remove();
                }

                $('#categoryFilter').val("0").trigger('change');

                if ( $item.parents('.list').is($('#search-results')) )
                {
                    $('.list-category .list-item[data-id=' + product + ']').remove();
                }

                // We add the product to the list
                $container.prepend(data);

                // We remove the product so we can't add it again and throw an error
                $item.remove();
            }
        });
    });

    $('#products').on('click', '.list-item .fa-remove', function(){
        var $item = $(this).parent();
        var product = $item.attr('data-id');

        var category = $item.attr('data-category');
        var $container = $('#category-' + category);

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

    $('#productSearch').submit(function(){
        var search = $('#search').val();

        $.ajax({
            url: Routing.generate('products_search'),
            type: 'GET',
            data: {search: search},
            success: function(data){
                $('#search-results').css("display", "block");

                $('#search-results .list-content').html(data);
            }
        });

        return false;
    });

    $('#categoryFilter').change(function(){
        var category = $(this).val();

        $.ajax({
            url: Routing.generate('lists_category_filter', {"list": list}),
            type: 'POST',
            data: {category: category},
            success: function(data){
                $('#products').html(data);

                // If we have a category (!= 0) we edit the delete button to delete the products in the category
                $link = $('#removeProducts');
                if ( category != 0 ) {
                    $link.attr("href", Routing.generate('lists_remove_category', {"list": list, "category": category}))
                    $link.html('<i class="fa fa-trash"></i> Supprimer les produits de la catégorie');
                } else {
                    $link.attr("href", Routing.generate('lists_remove_all_products', {"list": list}))
                    $link.html('<i class="fa fa-trash"></i> Supprimer tous les produits');
                }
            }
        });
    });

});