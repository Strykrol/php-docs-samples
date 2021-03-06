<?php
/**
 * Copyright 2020 Google LLC.
 *
 * Licensed under the Apache License, Version 2.0 (the 'License');
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an 'AS IS' BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

// [START functions_storage_unit_test]

namespace Google\Cloud\Samples\Functions\HelloworldStorage\Test;

use Google\CloudFunctions\CloudEvent;
use PHPUnit\Framework\TestCase;

/**
 * Class SampleUnitTest.
 *
 * Unit test for 'Helloworld Storage' Cloud Function.
 */
class SampleUnitTest extends TestCase
{
    /**
     * Include the Cloud Function code before running any tests.
     *
     * @see https://phpunit.readthedocs.io/en/latest/fixtures.html
     */
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/index.php';
    }

    public function dataProvider()
    {
        return [
            [
                'cloudevent' => CloudEvent::fromArray([
                    'id' => uniqid(),
                    'source' => 'storage.googleapis.com',
                    'specversion' => '1.0',
                    'type' => 'google.cloud.storage.object.v1.finalized',
                    'data' => [
                        'bucket' => 'some-bucket',
                        'metageneration' => '1',
                        'name' => 'folder/friendly.txt',
                        'timeCreated' => '2020-04-23T07:38:57.230Z',
                        'updated' => '2020-04-23T07:38:57.230Z',
                    ],
                ]),
                'statusCode' => '200',
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFunction(CloudEvent $cloudevent, string $statusCode): void
    {
        // Capture function output by overriding the function's logging behavior.
        // The 'LOGGER_OUTPUT' environment variable must be used in your function:
        //
        // $log = fopen(getenv('LOGGER_OUTPUT') ?: 'php://stderr', 'wb');
        // fwrite($log, 'Log Entry');
        putenv('LOGGER_OUTPUT=php://output');
        helloGCS($cloudevent);
        // Provided by PHPUnit\Framework\TestCase.
        $actual = $this->getActualOutput();
        
        // Test output includes the properties provided in the CloudEvent.
        foreach ($cloudevent->getData() as $property => $value) {
            $this->assertContains($value, $actual);
        }
        $this->assertContains($cloudevent->getId(), $actual);
        $this->assertContains($cloudevent->getType(), $actual);
    }
}

// [END functions_storage_unit_test]
