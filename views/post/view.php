<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Post */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$isAdmin = Yii::$app->user->can('admin');

$statusLabels = [
    'draft' => 'Draft',
    'pending' => 'Menunggu Review',
    'published' => 'Terbit',
    'rejected' => 'Ditolak',
];
?>

<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-default']) ?>

        <?php if ($isAdmin || in_array($model->status, ['draft', 'rejected'], true)): ?>
            <?= Html::a('Edit', ['update', 'id' => $model->idpost], [
                'class' => 'btn btn-primary'
            ]) ?>
        <?php endif; ?>

        <?php if (!$isAdmin && in_array($model->status, ['draft', 'rejected'], true)): ?>
            <?= Html::a('Kirim untuk Review', ['submit', 'id' => $model->idpost], [
                'class' => 'btn btn-success',
                'data' => [
                    'method' => 'post',
                    'confirm' => 'Kirim artikel ini untuk direview?',
                ],
            ]) ?>
        <?php endif; ?>

        <?php if ($isAdmin && $model->status === 'pending'): ?>
            <?= Html::a('Terbitkan Artikel', ['approve', 'id' => $model->idpost], [
                'class' => 'btn btn-success',
                'data' => [
                    'method' => 'post',
                    'confirm' => 'Setujui dan terbitkan artikel ini?',
                ],
            ]) ?>
        <?php endif; ?>

        <?php if ($isAdmin || in_array($model->status, ['draft', 'rejected'], true)): ?>
            <?= Html::a('Hapus', ['delete', 'id' => $model->idpost], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Yakin ingin menghapus artikel ini?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title:ntext',
            'content:html',
            'date',
            'username',
            [
                'label' => 'Status',
                'value' => isset($statusLabels[$model->status])
                    ? $statusLabels[$model->status]
                    : $model->status,
            ],
            [
                'label' => 'Alasan Penolakan',
                'attribute' => 'rejection_reason',
                'visible' => $model->status === 'rejected',
            ],
        ],
    ]) ?>

    <?php if ($isAdmin && $model->status === 'pending'): ?>
        <hr>

        <h3>Tolak Artikel</h3>

        <?= Html::beginForm(['reject', 'id' => $model->idpost], 'post') ?>

        <div class="form-group">
            <label for="rejection-reason">Alasan penolakan</label>

            <textarea
                id="rejection-reason"
                name="rejection_reason"
                class="form-control"
                rows="4"
                required
                placeholder="Jelaskan alasan artikel ditolak..."
            ></textarea>
        </div>

        <?= Html::submitButton('Tolak Artikel', [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Yakin ingin menolak artikel ini?',
            ],
        ]) ?>

        <?= Html::endForm() ?>

    <?php endif; ?>

</div>
