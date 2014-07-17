<?php /** @var $model Product */
cs()->registerPackage('angular');
?>
<style type="text/css">
    #custom-filters-app .table{
        margin-bottom: 0;
    }
    #custom-filters-app .table th{
        font-size: 13px;
        padding: 2px 5px;
    }
    #custom-filters-app .table td{
        font-size: 12px;
        padding: 2px 8px;
        background: #f9f9f9;
    }
    #custom-filters-app .table label {
        margin-bottom: 0;
        font-weight: normal;
    }
    #custom-filters-app .table td input[type=checkbox]{
        margin-top: 0px;
        top: 2px;
        position: relative;
    }
</style>
<div id="custom-filters-app" class="well" ng-app="CustomFiltersApp" ng-controller="CustomFiltersCtrl" ng-show="categoryId">
    <h4><?= t('Additional filters for category') ?>
        <small>{{categoryName}}</small>
    </h4>
    <table ng-repeat="filter in filters" class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th colspan="2">{{filter.name}}</th>
            </tr>
        </thead>
        <tbody ng-repeat="value in filter.values">
            <tr>
                <td><label for="filter-value-{{value.id}}">{{value.name}}</label></td>
                <td><input id="filter-value-{{value.id}}" type="checkbox" ng-click="select(value)" ng-checked="selected.indexOf(value) > -1"/></td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" ng-repeat="value in selected" value="{{value.id}}" name="Product[filterValues][]"/>
</div>
<script type="text/javascript">
    angular.module('CustomFiltersApp',[]).controller('CustomFiltersCtrl', function($scope, $http){
        $scope.product_id = <?= CJSON::encode($model->id) ?>;
        $scope.select = function(value){
            if($scope.selected.indexOf(value)>-1){
                $scope.selected.splice($scope.selected.indexOf(value),1);
            } else {
                $scope.selected.push(value);
            }
        };
        $scope.changeCategory = function(id){
            $scope.categoryId = id;
            id && $http.get('/admin/catalog/product/category-filters-data/'+id).then(function(response){
                $scope.categoryName = response.data.categoryName;
                $scope.filters = response.data.filters;
                if($scope.product_id){
                    $scope.selected = [];
                    $http.get('/admin/catalog/product/selected-filter-values/'+ $scope.product_id).then(function(response){
                        angular.forEach(response.data, function(selectedValueId){
                            angular.forEach($scope.filters, function(filter){
                                angular.forEach(filter.values, function(value){
                                    value.id == selectedValueId && $scope.selected.push(value);
                                });
                            });
                        });
                    });
                }
            });
        };

        $scope.changeCategory(<?= CJSON::encode($model->category_id) ?>);

        $('#Product_category_id').on('change',function(){
            $scope.changeCategory($(this).val());
        });
    });
</script>