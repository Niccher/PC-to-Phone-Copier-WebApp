# Testing Guide

This document details how to run automated tests for P2P Copier WebApp using PHPUnit.

---

## 1. Test Suite Configuration

Testing is configured in `phpunit.xml.dist` located at the root of the repository.

### Running All Tests
```bash
# Inside the docker container
docker compose exec p2p-copier vendor/bin/phpunit

# Or on host (if PHPUnit is installed via Composer)
vendor/bin/phpunit
```

### Running Specific Test Suites
```bash
# Run unit tests only
vendor/bin/phpunit --testsuite Unit

# Run controller and feature tests
vendor/bin/phpunit --testsuite Feature

# Run a single test file
vendor/bin/phpunit tests/app/Controllers/ApiTest.php
```

---

## 2. Writing New Tests

Feature tests extending `CodeIgniter\Test\CIUnitTestCase` or `CodeIgniter\Test\FeatureTestTrait`:

```php
<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class PingApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testPingReturnsOk()
    {
        $result = $this->get('api/v1/ping');
        $result->assertStatus(200);
        $result->assertJSONExact(['status' => 'ok', 'message' => 'pong']);
    }
}
```
