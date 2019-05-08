<?php

namespace detalika\clients\commands;
 
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
 
use detalika\clients\models\base\Type;
use detalika\clients\models\base\Source;
use detalika\clients\models\base\Shop;

class InitController extends Controller
{
    public function actionIndex()
    {
        echo 'yii init/create-client-types'.PHP_EOL;
        echo 'yii init/delete-client-types'.PHP_EOL;
        echo 'yii init/create-test-sources'.PHP_EOL;
        echo 'yii init/delete-test-sources'.PHP_EOL;
        echo 'yii init/create-test-shops'.PHP_EOL;
        echo 'yii init/delete-test-shops'.PHP_EOL;
    }
 
    public function actionCreateClientTypes()
    {        
        try {
            $result = Yii::$app->db->createCommand()
                ->batchInsert(
                    Type::tableName(), 
                    ['name', 'type'], 
                    $this->getInitialClientTypeDataItems()
                )->execute();
                
            $this->log($result > 0) ;
        }
        catch (\Exception $e) {
            $this->log(false);
        }
    }
    
    public function actionDeleteClientTypes()
    {        
        try {
            $names = [];
            foreach ($this->getInitialClientTypeDataItems() as $item) {
                $names []= $item[0];
            }

            $result = Type::deleteAll(['in', 'name', $names]);
            
            $this->log($result > 0) ;
        }
        catch (\Exception $e) {
            $this->log(false);
        }
    }
    
    public function actionCreateTestSources()
    {        
        try {
            $nameField = Source::getFieldName('name');
            $result = Yii::$app->db->createCommand()
                ->batchInsert(
                    Source::tableName(), 
                    [$nameField], 
                    $this->getTestSourceDataItems()
                )->execute();
                
            $this->log($result > 0) ;
        }
        catch (\Exception $e) {
            $this->log(false);
        }
    }
    
    public function actionDeleteTestSources()
    {        
        try {
            $names = [];
            foreach ($this->getTestSourceDataItems() as $item) {
                $names []= $item[0];
            }

            $nameField = Source::getFieldName('name');
            $result = Source::deleteAll(['in', $nameField, $names]);
            
            $this->log($result > 0) ;
        }
        catch (\Exception $e) {
            $this->log(false);
        }
    }
    
    public function actionCreateTestShops()
    {        
        try {
            $nameField = Shop::getFieldName('name');
            $result = Yii::$app->db->createCommand()
                ->batchInsert(
                    Shop::tableName(), 
                    [$nameField], 
                    $this->getTestShopDataItems()
                )->execute();
                
            $this->log($result > 0) ;
        }
        catch (\Exception $e) {
            $this->log(false);
        }
    }
    
    public function actionDeleteTestShops()
    {        
        try {
            $names = [];
            foreach ($this->getTestShopDataItems() as $item) {
                $names []= $item[0];
            }

            $nameField = Shop::getFieldName('name');
            $result = Shop::deleteAll(['in', $nameField, $names]);
            
            $this->log($result > 0) ;
        }
        catch (\Exception $e) {
            $this->log(false);
        }
    }
    
    private function log($success)
    {
        if ($success) {
            $this->stdout('Success!', Console::FG_GREEN, Console::BOLD);
        } else {
            $this->stderr('Error!', Console::FG_RED, Console::BOLD);
        }
        echo PHP_EOL;
    }
    
    private function getInitialClientTypeDataItems() 
    {
        return [
            [Type::NAME_PRIVATE_PERSON, Type::TYPE_INDIVIDUAL],
            ['Автосервис', Type::TYPE_LEGAL],
            ['Автопарк', Type::TYPE_LEGAL],
        ]; 
    }
    
    private function getTestSourceDataItems()
    {
        return [
            ['Источник 1'],
            ['Источник 2'],
            ['Источник 3'],
        ];
    }
     
    private function getTestShopDataItems()
    {
        return [
            ['Магазин 1'],
            ['Магазин 2'],
            ['Магазин 3'],
        ];
    }
}
