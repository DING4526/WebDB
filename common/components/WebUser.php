<?php

namespace common\components;

use yii\web\User as YiiWebUser;
use common\models\User;

class WebUser extends YiiWebUser
{
    /**
     * 强类型 User Identity
     */
    public function getUser(): ?User
    {
        $identity = parent::getIdentity();
        return $identity instanceof User ? $identity : null;
    }
}
