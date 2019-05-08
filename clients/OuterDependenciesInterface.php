<?php

namespace detalika\clients;

interface OuterDependenciesInterface 
{
    public function getShopsTableName();
    public function getShopsIdFieldName();
    public function getShopsNameFieldName();
    
    public function getUsersTableName();
    public function getUsersIdFieldName();
    public function getUsersEmailFieldName();
        
    public function getPickingProfileUsersTableName();
    public function getPickingProfileUsersProfileIdFieldName();
    public function getPickingProfileUsersUserIdFieldName();
    
    public function getAuthUsersAjaxRoute();
    public function getCurrentProfile();

    public function getOrdersTableName();
    public function getOrdersIdFieldName();
    public function getOrdersProfileIdFieldName();
    public function getOrdersModelClass();

    public function getRequestsTableName();
    public function getRequestsIdFieldName();
    public function getRequestsProfileIdFieldName();
    public function getRequestsModelClass();

    public function getRequestsClientCarProfilesTableName();
    public function getRequestsClientCarProfilesIdFieldName();
    public function getRequestsClientCarProfilesProfileIdFieldName();
    public function getRequestsClientCarProfilesModelClass();
}