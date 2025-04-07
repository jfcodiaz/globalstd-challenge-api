<?php

namespace Tests;

use Faker\Generator as FakerGenerator;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected FakerGenerator $faker;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->faker = \Faker\Factory::create();
    }
}
