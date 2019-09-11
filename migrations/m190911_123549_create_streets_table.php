<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%streets}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%city_ref}}`
 */
class m190911_123549_create_streets_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%streets}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'ref' => $this->string()->notNull()->unique(),
            'city_ref' => $this->string()->notNull(),
        ]);
        
        // creates index for column `city_ref`
        $this->createIndex(
            '{{%idx-streets-city_ref}}',
            '{{%streets}}',
            'city_ref'
        );
        
        // add foreign key for table `{{%cities}}`
        $this->addForeignKey(
            '{{%fk-streets-city_ref}}',
            '{{%streets}}',
            'city_ref',
            '{{%cities}}',
            'ref',
            'CASCADE'
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%cities}}`
        $this->dropForeignKey(
            '{{%fk-streets-city_ref}}',
            '{{%streets}}'
        );
        
        // drops index for column `city_ref`
        $this->dropIndex(
            '{{%idx-streets-city_ref}}',
            '{{%streets}}'
        );
        
        $this->dropTable('{{%streets}}');
    }
}
