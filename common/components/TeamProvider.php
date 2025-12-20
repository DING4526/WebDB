<?php

namespace common\components;

use Yii;
use common\models\Team;
use yii\base\Component;

/**
 * 提供单团队信息的统一入口，优先使用配置常量，必要时自动创建。
 */
class TeamProvider extends Component
{
    /** @var array 默认团队配置，来源 params['team'] */
    public $defaults = [];

    /** @var Team|null */
    private $cachedTeam;

    public function getId(): ?int
    {
        return $this->getTeam()->id ?? null;
    }

    public function getName(): ?string
    {
        return $this->getTeam()->name ?? null;
    }

    public function getTeam(): ?Team
    {
        if ($this->cachedTeam !== null) {
            return $this->cachedTeam;
        }

        $config = $this->defaults ?: (Yii::$app->params['team'] ?? []);
        $id = $config['id'] ?? null;
        if ($id === null) {
            return null;
        }

        $team = Team::findOne($id);
        if (!$team) {
            $team = new Team();
            $team->id = $id;
            $team->name = $config['name'] ?? '默认团队';
            $team->topic = $config['topic'] ?? '';
            $team->intro = $config['intro'] ?? '';
            $team->status = $config['status'] ?? Team::STATUS_ACTIVE;
            $team->created_at = time();
            $team->updated_at = time();
            $team->save(false);
        }

        $this->cachedTeam = $team;
        return $team;
    }
}
