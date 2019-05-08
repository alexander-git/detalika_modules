<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 2/15/17
 * Time: 10:51 AM
 */

namespace detalika\delivery\fixtures;


use detalika\delivery\models\Partner;
use yii\test\ActiveFixture;

class PartnerFixture extends ActiveFixture
{
    public $modelClass = Partner::class;
    public function getData() {
        $data = [];
        $faker = \Faker\Factory::create();
        for ($key = 0; $key < 10; $key++) {
            $data[] = [
                'name' => $faker->company,
            ];
        }

        return $data;
    }
}