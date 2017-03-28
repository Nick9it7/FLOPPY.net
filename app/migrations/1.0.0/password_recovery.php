<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class PasswordRecoveryMigration_100
 */
class PasswordRecoveryMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('password_recovery', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 11,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'user',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size' => 11,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'hash',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'user'
                        ]
                    ),
                    new Column(
                        'active',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'hash'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY')
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '36',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_general_ci'
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        $this->morphTable('password_recovery', [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 11,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'user',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size' => 11,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'hash',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'user'
                        ]
                    ),
                    new Column(
                        'active',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 255,
                            'after' => 'hash'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['id'], 'PRIMARY')
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '36',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_general_ci'
                ],
            ]
        );

    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
