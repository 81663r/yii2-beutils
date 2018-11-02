<?php
use yii\widgets\ListView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\beutils\modules\apimgr\models\UserModel;


$query = (new Query())->select("*")->from('api_user');

$dataProvider = new ActiveDataProvider([
    'query' => $query,
    'pagination' => [
        'pageSize' => 20,
    ],
]);

// Create user mode
$umodel = new UserModel();
?>
<div class="jumbotron">
    <h1>API Manager</h1>
    <p></p>
</div>

<div class="container">

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#users" aria-controls="home" role="tab" data-toggle="tab">Users</a></li>
        <li role="presentation"><a href="#apis" aria-controls="apis" role="tab" data-toggle="tab">APIs</a></li>
        <li role="presentation"><a href="#permissions" aria-controls="permissions" role="tab" data-toggle="tab">Permissions</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="users">
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'id',
                    'domain',
                    'username',
                    'password',
                    'status',
                    'creation_date',
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete} {toggle}',
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['testone', 'id' => 2]);
                            },
                            'toggle' => function($url, $model, $key){
                                return Html::a('<span class="glyphicon glyphicon-adjust"></span>', ['testone', 'id' => 2]);
                            }
                        ],
                    ],
                ],
            ]);
            ?>
            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myDialog">
                <span class="glyphicon glyphicon-plus-sign"></span>
                add user
            </button>
        </div>

        <div role="tabpanel" class="tab-pane" id="apis">
        </div>

        <div role="tabpanel" class="tab-pane" id="permissions">
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="myDialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <?php $form = ActiveForm::begin(['action'=>['newuser']]); ?>
                        <div class="form-group">
                            <?= $form->field($umodel, 'email') ?>
                        </div>
                        <div class="form-group">
                            <?=$form->field($umodel, 'password')?>
                        </div>
                        <div class="form-group">
                            <?=$form->field($umodel, 'domain')?>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <?=Html::submitButton('Submit', ['class' => 'btn btn-primary'])?>
                </div>
                <?php ActiveForm::end(); ?>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

</div>