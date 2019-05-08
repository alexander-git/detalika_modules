<?php

namespace detalika\requests\models\user\relation;

use detalika\requests\models\user\search\RequestPositionSearch;

class RequestPositionInRequestSearch extends RequestPositionSearch
{
    use UserNavigationFactoryTrait;
    
    public function formName()
    {
        return 'requestPosition';
    }
    
    public function getGridColumns()
    {
        $columns = parent::getGridColumns();
        unset($columns['requests_request_id']);
  
        return $columns;
    }
}