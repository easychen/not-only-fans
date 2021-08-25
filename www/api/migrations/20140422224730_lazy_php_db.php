<?php

use Phinx\Migration\AbstractMigration;

class LazyPhpDb extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
    */
    public function change()
    { 
        $table = $this->table('lptest');
        $table->addColumn('name', 'string' , array('limit' => 20) )
              ->addColumn('password', 'string' , array('limit' => 20) )
              ->addColumn('avatar', 'string' , array('limit' => 255) )
              ->addColumn('created', 'datetime')
              ->create();
    } 
    
    
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}