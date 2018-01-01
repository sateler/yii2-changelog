<?php

namespace sateler\changelog\migrations;

use yii\db\Migration;

/**
 * Class m171229_203346_add_changelog_model
 */
class m171229_203346_add_changelog_model extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('changelog', [
            'id' => $this->primaryKey(),
            'change_uuid' => $this->string(36)->notNull(),
            'change_type' => $this->string(10)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'user_id' => $this->bigInteger()->null(),
            'table_name' => $this->string(255)->notNull(),
            'column_name' => $this->string(255)->notNull(),
            'row_id' => $this->bigInteger()->null(),
            'old_value' => $this->string(255)->null(),
            'new_value' => $this->string(255)->null(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('changelog');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171229_203346_add_changelog_model cannot be reverted.\n";

        return false;
    }
    */
}
