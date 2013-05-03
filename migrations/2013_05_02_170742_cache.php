<?php

class Zillow_Cache {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zillow', function($table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->string('hash', 32);
            $table->text('response');
            $table->boolean('is_success');
            $table->index('hash');
            $table->index('is_success');
        });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('zillow');
    }

}