<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m190131_094452_create_user_table extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'authKey' => $this->string(32)->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'email_confirm_token' => $this->string(),
            'password_hash' => $this->string()->notNull(),
            'is_active' => $this->smallInteger()->notNull()->defaultValue(5),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('user');
    }
}
