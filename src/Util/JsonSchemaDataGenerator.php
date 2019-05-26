<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Util;

use Faker\Factory;

class JsonSchemaDataGenerator
{
    public function generate(string $jsonSchema): array
    {
        return $this->getRecord(json_decode($jsonSchema, true));
    }

    private function getRecord(array $jsonSchemaData): array
    {
        $faker = Factory::create();

        $record = [];

        if (isset($jsonSchemaData['properties'])) {
            foreach ($jsonSchemaData['properties'] as $propertyName => $propertyDefinition) {
                $propertyType = $propertyDefinition['type'];
                if (is_array($propertyType)) { // may be [string, null]
                    foreach ($propertyType as $pt) {
                        if ($pt !== null) {
                            $propertyType = $pt;
                            break;
                        }
                    }
                }

                if ($propertyType == 'string') {
                    if (isset($propertyDefinition['format'])
                        && $propertyDefinition['format'] == 'date-time'
                    ) {
                        $record[$propertyName] = $faker->date('c');
                    } elseif (isset($propertyDefinition['pattern'])
                        && $propertyDefinition['pattern'] == '\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d'
                    ) {
                        $record[$propertyName] = $faker->date('Y-m-d H:i:s');
                    } else {
                        $record[$propertyName] = $faker->text;
                    }
                } elseif ($propertyType == 'boolean') {
                    $record[$propertyName] = $faker->boolean;
                } elseif ($propertyType == 'integer') {
                    $record[$propertyName] = $faker->randomNumber();
                } elseif ($propertyType == 'number') {
                    $record[$propertyName] = $faker->randomFloat();
                } elseif ($propertyType == 'array') {
                    $array = [];

                    $pdType = $propertyDefinition['items']['type'];
                    if (is_array($pdType)) {
                        foreach ($propertyDefinition['items']['type']  as $pdt) {
                            if ($pdt != 'null') {
                                $pdType = $pdt;
                            }
                        }
                    }

                    for ($i=0; $i<rand(0, 10); $i++) {
                        switch ($pdType) {
                            case 'string':
                                $array[] = $faker->text;
                                break;
                            case 'integer':
                                $array[] = $faker->randomNumber();
                                break;
                            case 'object':
                                $array[] = $this->getRecord($propertyDefinition['items'], false);
                                break;
                            default:
                                throw new \Exception('Not supported');
                        }
                    }

                    $record[$propertyName] = $array;
                } elseif ($propertyType == 'object') {
                    $record[$propertyName] = $this->getRecord($propertyDefinition, false);
                } else {
                    $msg = sprintf(
                        'Property %s with type %s not supported',
                        $propertyName,
                        json_encode($propertyDefinition['type'])
                    );

                    throw new \Exception($msg);
                }
            }
        }

        return $record;
    }
}
