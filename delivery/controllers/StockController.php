<?php

namespace detalika\delivery\controllers;

use detalika\delivery\common\CrudController;
use detalika\delivery\models\forms\StockForm;
use detalika\delivery\models\search\StockSearch;

class StockController extends CrudController
{
    
    protected function getSearchModelClassName()
    {
        return StockSearch::className();
    }
    
    protected function getEditModelClassName() 
    {
        return StockForm::className();
    }
    
    protected function getModelsListTitle() 
    {
        return 'Склады';
    }
}
