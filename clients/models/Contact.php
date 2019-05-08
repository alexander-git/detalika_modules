<?php

namespace detalika\clients\models;

use detalika\clients\models\base\Contact as BaseContact;

class Contact extends BaseContact
{
    public static function createFromTypeAndValue($type, $value) {
        $contact = new Contact();
        $contact->attributes = [
            'clients_contacts_type_id' => ContactType::getIdByType($type),
            'value' => $value,
        ];

        return $contact;
    }
}