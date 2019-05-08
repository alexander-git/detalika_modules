<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 2/14/17
 * Time: 11:11 AM
 */

namespace detalika\delivery\forms\search;

use execut\actions\action\adapter\gridView\ModelHelper;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class Partner extends \detalika\delivery\models\Partner
{
    use ModelHelper;
    public $term = null;
    public function rules()
    {
        return [
            [['term'], 'safe'],
            [$this->attributes(), 'safe']
        ];
    }

    public function getGridColumns() {
        $columns = $this->getStandardColumns();
        unset($columns['visible']);
        return ArrayHelper::merge($columns, [
        ]);
    }

    public function search()
    {
        $q = self::find();

        $this->standardFind($q);

        $dataProvider = new ActiveDataProvider([
            'query' => $q,
        ]);

        return $dataProvider;
    }
}