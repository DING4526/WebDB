<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\LoginForm;
use common\models\TeamMember;
use common\models\TeamMemberApply;
use common\models\WarMessage;
use common\models\WarEvent;
use common\models\WarPerson;
use common\models\WarMedia;
use common\models\WarVisitLog;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'help', 'dashboard-data'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Help page action.
     * 显示帮助信息（角色与权限模型、成员注册/管理链路）
     *
     * @return string
     */
    public function actionHelp()
    {
        return $this->renderPartial('help');
    }

    /**
     * Dashboard data API.
     * 返回仪表板所需的统计数据
     *
     * @return Response
     */
    public function actionDashboardData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $teamId = Yii::$app->teamProvider ? Yii::$app->teamProvider->getId() : null;

        // 基础统计
        $memberCount = TeamMember::find()
            ->andWhere(['team_id' => $teamId, 'status' => TeamMember::STATUS_ACTIVE])
            ->count();

        $pendingApplyCount = TeamMemberApply::find()
            ->andWhere(['team_id' => $teamId, 'status' => TeamMemberApply::STATUS_PENDING])
            ->count();

        $pendingMessageCount = WarMessage::find()
            ->andWhere(['status' => WarMessage::STATUS_PENDING])
            ->count();

        $eventCount = WarEvent::find()->count();
        $personCount = WarPerson::find()->count();
        $mediaCount = WarMedia::find()->count();

        // 近7天访问量
        $sevenDaysAgo = strtotime('-7 days');
        $visits7Days = WarVisitLog::find()
            ->andWhere(['>=', 'visited_at', $sevenDaysAgo])
            ->count();

        // 近7天新增内容
        $newEvents7Days = WarEvent::find()
            ->andWhere(['>=', 'created_at', $sevenDaysAgo])
            ->count();
        $newPersons7Days = WarPerson::find()
            ->andWhere(['>=', 'created_at', $sevenDaysAgo])
            ->count();
        $newContent7Days = $newEvents7Days + $newPersons7Days;

        // 访问趋势数据（按天）
        $visitTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $dayStart = strtotime("-{$i} days 00:00:00");
            $dayEnd = strtotime("-{$i} days 23:59:59");
            $count = WarVisitLog::find()
                ->andWhere(['>=', 'visited_at', $dayStart])
                ->andWhere(['<=', 'visited_at', $dayEnd])
                ->count();
            $visitTrend[] = [
                'date' => date('m-d', $dayStart),
                'count' => (int)$count,
            ];
        }

        // 留言趋势数据（按天，分状态）
        $messageTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $dayStart = strtotime("-{$i} days 00:00:00");
            $dayEnd = strtotime("-{$i} days 23:59:59");
            $pending = WarMessage::find()
                ->andWhere(['>=', 'created_at', $dayStart])
                ->andWhere(['<=', 'created_at', $dayEnd])
                ->andWhere(['status' => WarMessage::STATUS_PENDING])
                ->count();
            $approved = WarMessage::find()
                ->andWhere(['>=', 'created_at', $dayStart])
                ->andWhere(['<=', 'created_at', $dayEnd])
                ->andWhere(['status' => WarMessage::STATUS_APPROVED])
                ->count();
            $rejected = WarMessage::find()
                ->andWhere(['>=', 'created_at', $dayStart])
                ->andWhere(['<=', 'created_at', $dayEnd])
                ->andWhere(['status' => WarMessage::STATUS_REJECTED])
                ->count();
            $messageTrend[] = [
                'date' => date('m-d', $dayStart),
                'pending' => (int)$pending,
                'approved' => (int)$approved,
                'rejected' => (int)$rejected,
            ];
        }

        // 内容质量概览
        $totalEvents = (int)$eventCount;
        $eventsWithCover = WarEvent::find()
            ->innerJoinWith('coverImage')
            ->count();
        $eventsWithSummary = WarEvent::find()
            ->andWhere(['not', ['summary' => null]])
            ->andWhere(['!=', 'summary', ''])
            ->count();
        $eventsWithPerson = WarEvent::find()
            ->innerJoinWith('eventPeople')
            ->distinct()
            ->count();

        $totalPersons = (int)$personCount;
        $personsWithIntro = WarPerson::find()
            ->andWhere(['not', ['intro' => null]])
            ->andWhere(['!=', 'intro', ''])
            ->count();
        $personsWithCover = WarPerson::find()
            ->innerJoinWith('coverImage')
            ->count();

        // 热榜TOP5
        $topEvents = WarVisitLog::find()
            ->select(['target_id', 'COUNT(*) as visit_count'])
            ->andWhere(['target_type' => 'event'])
            ->groupBy('target_id')
            ->orderBy(['visit_count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();
        
        $topEventDetails = [];
        foreach ($topEvents as $item) {
            $event = WarEvent::findOne($item['target_id']);
            if ($event) {
                $topEventDetails[] = [
                    'id' => $event->id,
                    'title' => $event->title,
                    'visits' => (int)$item['visit_count'],
                ];
            }
        }

        $topPersons = WarVisitLog::find()
            ->select(['target_id', 'COUNT(*) as visit_count'])
            ->andWhere(['target_type' => 'person'])
            ->groupBy('target_id')
            ->orderBy(['visit_count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        $topPersonDetails = [];
        foreach ($topPersons as $item) {
            $person = WarPerson::findOne($item['target_id']);
            if ($person) {
                $topPersonDetails[] = [
                    'id' => $person->id,
                    'name' => $person->name,
                    'visits' => (int)$item['visit_count'],
                ];
            }
        }

        return [
            'memberCount' => (int)$memberCount,
            'pendingApplyCount' => (int)$pendingApplyCount,
            'pendingMessageCount' => (int)$pendingMessageCount,
            'eventCount' => $totalEvents,
            'personCount' => $totalPersons,
            'mediaCount' => (int)$mediaCount,
            'visits7Days' => (int)$visits7Days,
            'newContent7Days' => (int)$newContent7Days,
            'visitTrend' => $visitTrend,
            'messageTrend' => $messageTrend,
            'quality' => [
                'eventCover' => $totalEvents > 0 ? round($eventsWithCover / $totalEvents * 100) : 0,
                'eventSummary' => $totalEvents > 0 ? round($eventsWithSummary / $totalEvents * 100) : 0,
                'eventPerson' => $totalEvents > 0 ? round($eventsWithPerson / $totalEvents * 100) : 0,
                'personIntro' => $totalPersons > 0 ? round($personsWithIntro / $totalPersons * 100) : 0,
                'personCover' => $totalPersons > 0 ? round($personsWithCover / $totalPersons * 100) : 0,
            ],
            'topEvents' => $topEventDetails,
            'topPersons' => $topPersonDetails,
        ];
    }
}
