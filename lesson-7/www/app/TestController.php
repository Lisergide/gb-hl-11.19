<?php
namespace app\controllers;
use Yii;
use app\models\Customers;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\widgets\RedisCacheProvider;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PhpAmqpLib\Message\AMQPMessage;
/**
 * CustomersController implements the CRUD actions for Customers model.
 */
class TestController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    public function createQueue(string $nameQueue)
    {
        try {
            // соединяемся с RabbitMQ
            $connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test');
            // Создаем канал общения с очередью
            $channel = $connection->channel();
            $channel->queue_declare($nameQueue, false, true, false, false);
            // создаём сообщение
            $msg = new AMQPMessage($nameQueue);
            // размещаем сообщение в очереди
            $channel->basic_publish($msg, '', $nameQueue);
            // закрываем соединения
            $channel->close();
            $connection->close();
        } catch (AMQPProtocolChannelException $e) {
            echo $e->getMessage();
        } catch (AMQPException $e) {
            echo $e->getMessage();
        }
    }
    /**
     * Lists all Customers models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->createQueue('Coffee');
        //Yii::$app->queue->push(['qweqwe']);
        //$queueName = 'Coffee';
        //$queue = $this->context->createQueue($queueName);
        //$this->context->declareQueue($queue);
        return $this->renderContent('Coffee queue created');
        /*return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);*/
    }
    /**
     * Create queue
     * @return mixed
     */
    public function actionCoffee()
    {
        $this->createQueue('Coffee');
        return $this->renderContent('Coffee queue created');
    }
    /**
     * Create queue.
     * @return mixed
     */
    public function actionPizza()
    {
        $this->createQueue('Pizza');
        return $this->renderContent('Pizza queue created');
    }
    /**
     * Displays a single Customers model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $cache = new RedisCacheProvider();
        $cache->set('model', $this->findModel($id), 600);
        return $this->render('view', [
            'model' => $cache->get('model'),
        ]);
    }
    /**
     * Creates a new Customers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customers();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->customerNumber]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing Customers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->customerNumber]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    /**
     * Deletes an existing Customers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    /**
     * Finds the Customers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Customers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customers::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}