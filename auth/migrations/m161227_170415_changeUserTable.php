<?php

use yii\db\Migration;

class m161227_170415_changeUserTable extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE {{%user%}} ALTER COLUMN username DROP NOT NULL');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE {{%user%}} ALTER COLUMN username SET NOT NULL');
    }

}
