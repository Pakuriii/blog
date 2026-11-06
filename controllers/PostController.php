<?php

namespace app\controllers;

use Yii;
use app\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
	'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view', 'submit', 'approve', 'reject'],
                        'allow' => true,
                        'roles' => ['author', 'admin'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'submit' => ['POST'],
                    'approve' => ['POST'],
                    'reject' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Post::find()->orderBy(['date' => SORT_DESC]);

        if (!$this->isAdmin()) {
            $query->andWhere([
                'username' => Yii::$app->user->identity->username
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    private function isAdmin()
    {
        return Yii::$app->user->can('admin');
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Post();

        if ($model->load(Yii::$app->request->post())) {
		$model->date = date('Y-m-d H:i:s');
		$model->username = \Yii::$app->user->identity->username;
        $model->status = 'draft';
        $model->rejection_reason = null;
		if($model->save()){
            return $this->redirect(['view', 'id' => $model->idpost]);
		}
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$this->isAdmin() &&
            !in_array($model->status, ['draft', 'rejected'], true)) {
            Yii::$app->session->setFlash(
                'error',
                'Artikel yang sedang direview atau sudah terbit tidak bisa diedit.'
            );

            return $this->redirect(['view', 'id' => $id]);
        }

        if ($model->load(Yii::$app->request->post())) {
            if (!$this->isAdmin()) {
                $model->username = Yii::$app->user->identity->username;
            }

            $model->date = date('Y-m-d H:i:s');

            if ($model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    'Artikel berhasil diperbarui.'
                );

                return $this->redirect(['view', 'id' => $model->idpost]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$this->isAdmin() &&
            !in_array($model->status, ['draft', 'rejected'], true)) {
            Yii::$app->session->setFlash(
                'error',
                'Artikel ini tidak bisa dihapus.'
            );

            return $this->redirect(['index']);
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    
    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSubmit($id)
    {
        $model = $this->findModel($id);

        if ($this->isAdmin()) {
            throw new NotFoundHttpException('Aksi tidak tersedia.');
        }

        if (!in_array($model->status, ['draft', 'rejected'], true)) {
            Yii::$app->session->setFlash('error', 'Artikel tidak bisa dikirim untuk review.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->status = 'pending';
        $model->rejection_reason = null;

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Artikel berhasil dikirim untuk review.');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionApprove($id)
    {
        if (!$this->isAdmin()) {
            throw new NotFoundHttpException('Aksi tidak tersedia.');
        }

        $model = $this->findModel($id);

        if ($model->status === 'pending') {
            $model->status = 'published';
            $model->rejection_reason = null;
            $model->save(false);

            Yii::$app->session->setFlash('success', 'Artikel berhasil diterbitkan.');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionReject($id)
    {
        if (!$this->isAdmin()) {
            throw new NotFoundHttpException('Aksi tidak tersedia.');
        }

        $model = $this->findModel($id);
        $reason = trim(Yii::$app->request->post('rejection_reason', ''));

        if ($model->status === 'pending' && $reason !== '') {
            $model->status = 'rejected';
            $model->rejection_reason = $reason;
            $model->save(false);

            Yii::$app->session->setFlash('success', 'Artikel berhasil ditolak.');
        } else {
            Yii::$app->session->setFlash('error', 'Alasan penolakan wajib diisi.');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {

            if (!$this->isAdmin() &&
                $model->username !== Yii::$app->user->identity->username) {
                throw new NotFoundHttpException('Artikel tidak ditemukan.');
            }

            return $model;
        }

        throw new NotFoundHttpException('Artikel tidak ditemukan.');
    }
}
