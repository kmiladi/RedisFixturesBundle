<?php

namespace Lab5Com\RedisFixturesBundle;

interface RedisFixtureInterface
{
    /**
     * Get an array of redis entries in the form of key => value.
     *
     * @return array
     */
    public function getData();
}
