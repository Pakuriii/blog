<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
$isAdmin = Yii::$app->user->can('admin');

$statusLabels = [
    'draft' => 'Draft',
    'pending' => 'Menunggu Review',
    'published' => 'Terbit',
    'rejected' => 'Ditolak',
];  
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Post', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title:ntext',
            'date',
            'username',

            [
                'attribute' => 'status',
                'label' => 'Status',
                'value' => function ($model) use ($statusLabels) {
                    return isset($statusLabels[$model->status])
                        ? $statusLabels[$model->status]
                        : $model->status;
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) use ($isAdmin) {
                        if ($isAdmin || in_array($model->status, ['draft', 'rejected'], true)) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                $url,
                                ['title' => 'Edit']
                            );
                        }
                        return '';
                    },

                    'delete' => function ($url, $model) use ($isAdmin) {
                        if ($isAdmin || in_array($model->status, ['draft', 'rejected'], true)) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-trash"></span>',
                                $url,
                                [
                                    'title' => 'Hapus',
                                    'data-confirm' => 'Yakin ingin menghapus artikel ini?',
                                    'data-method' => 'post',
                                ]
                            );
                        }
                        return '';
                    },
                ],
            ],
        ],
    ]); ?>
</div>
