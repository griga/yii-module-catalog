<?php
/** Created by griga at 01.07.2014 | 19:46.
 * @var Product $model
 * @var BackEndController $this
 */
?>
<div class="well" id="related-products-app" data-product-id="<?= $model->id ?>">
    <h4><?= t('Related products') ?></h4>
    <?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
        'name' => 'related_product_search',
        'sourceUrl' => '/admin/catalog/product/relatedAutoComplete',
        'options' => array(
            'minLength' => '2',
            'focus' => 'js: function(event, ui) {
                                $(".add-related-btn").val(ui.item.label);
                                return false;
                            }',
            'select' => 'js: function (event, ui) {
                                $(".add-related-inp").val(ui.item.label);
                                $(".add-related-btn").data("product", {
                                    id:ui.item.value,
                                    name:ui.item.label
                                });
                                return false;
                            }'
        ),
        'htmlOptions' => array(
            'class' => 'add-related-inp form-control-inline col-sm-6',
            'placeholder' => t('Product search'),
        ),
    ));?>&nbsp;
    <button class="btn btn-success add-related-btn"><?= t('Add') ?></button>
    <table class="table table-bordered table-condensed table-white">
        <thead>
        <tr>
            <th><?= t('Name') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($model->relatedProducts as $related): ?>
            <tr>
                <td><input type="hidden" name="Product[relatedProducts][]"
                           value="<?= $related->id ?>"><?= $related->name ?></td>
                <td><a href="#"><i class="glyphicon glyphicon-trash"></i><a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <script type="text/javascript">
        $(function () {
            var $relApp = $('#related-products-app');
            $relApp.on('click', '.add-related-btn', function (e) {
                var data = $(this).data('product');
                if (data.id) {
                    $('tbody', $relApp).append($('<tr><td>' +
                    '<input type="hidden" name="Product[relatedProducts][]" value="' + data.id + '">' + data.name + '</td>' +
                    '<td><a href="#"><i class="glyphicon glyphicon-trash"></i></a></td></tr>'));
                }
                return false;
            }).on('click', '.glyphicon-trash', function (e) {
                $(this).closest('tr').fadeOut('fast', function () {
                    $(this).remove();
                });
                e.preventDefault();
                return false;
            });
        });
    </script>
</div>
