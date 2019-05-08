<?php

namespace detalika\delivery\queries;

/**
 * This is the ActiveQuery class for [[\detalika\delivery\models\Partner]].
 *
 * @see \detalika\delivery\models\Partner
 */
class PartnerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \detalika\delivery\models\Partner[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \detalika\delivery\models\Partner|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
