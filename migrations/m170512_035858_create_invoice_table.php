<?php

use yii\db\Migration;

/**
 * Handles the creation of table `invoice`.
 */
class m170512_035858_create_invoice_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%invoice}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'order_id' => $this->integer()->null(),
            'sum' => $this->decimal(10, 2)->notNull(),
            'status' => $this->string(1)->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'pay_time' => $this->timestamp()->null(),
            'method' => $this->string(7)->notNull(),
            'orderId' => $this->string()->null(),
            'remote_id' => $this->integer()->null(),
            'data' => $this->json()->null(),
            'url' => $this->string()->null(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%invoice}}');
    }
}
