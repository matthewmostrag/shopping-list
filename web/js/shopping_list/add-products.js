$(document).ready(function(){

    // Select2
    $('#appbundle_product_category, #categoryFilter').select2({
        placeholder: 'Catégorie',
        minimumResultsForSearch: Infinity
    });

    var list = $('#listId').val();
    var $container = $('#products');
    var $default = $container.find('.default');

    // Adding a new product
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

    // Adding an existing product from the categories to the list
    $('ul[id^=category-], #search-results').on('click', '.list-item .fa-plus', function(){
        // The list item
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

                // We reset the category filter
                $('#categoryFilter').val("0").trigger('change');

                // If we added the product from the research frame we need to remove it also from the categories frames
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

    // Remove a product from the list
    $('#products').on('click', '.list-item .fa-remove', function(){
        // The list item
        var $item = $(this).parent();
        var product = $item.attr('data-id');

        var category = $item.attr('data-category');
        // The category container where to add the product
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

    // Product research
    $('#productSearch').submit(function(){
        // The search term
        var search = $('#search').val();

        $.ajax({
            url: Routing.generate('products_search'),
            type: 'GET',
            data: {search: search},
            success: function(data){
                // We display the search results container, hidden by default
                $('#search-results').css("display", "block");

                // We put the results in it
                $('#search-results .list-content').html(data);
            }
        });

        return false;
    });

    // Category filter
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
                    // If the category is null we want to display them all, so we reset the link
                    $link.attr("href", Routing.generate('lists_remove_all_products', {"list": list}))
                    $link.html('<i class="fa fa-trash"></i> Supprimer tous les produits');
                }
            }
        });
    });

});