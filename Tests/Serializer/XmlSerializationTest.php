<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Tests\Serializer;

use JMS\SerializerBundle\Tests\Fixtures\InvalidUsageOfXmlValue;
use JMS\SerializerBundle\Exception\InvalidArgumentException;
use JMS\SerializerBundle\Annotation\Type;
use JMS\SerializerBundle\Annotation\XmlValue;

class XmlSerializationTest extends BaseSerializationTest
{
    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidUsageOfXmlValue()
    {
        $obj = new InvalidUsageOfXmlValue();
        $this->serialize($obj);
    }

    public function testExternalEntitiesAreDisabledByDefault()
    {
        $currentDir = getcwd();
        chdir(__DIR__);
        $entity = $this->deserialize('<?xml version="1.0"?>
            <!DOCTYPE author [
                <!ENTITY foo SYSTEM "php://filter/read=convert.base64-encode/resource='.basename(__FILE__).'">
            ]>
            <result>
                &foo;
            </result>', 'JMS\SerializerBundle\Tests\Serializer\ExternalEntityTest');
        chdir($currentDir);

        $this->assertEquals('', trim($entity->foo));
    }

    protected function getContent($key)
    {
        if (!file_exists($file = __DIR__.'/xml/'.$key.'.xml')) {
            throw new InvalidArgumentException(sprintf('The key "%s" is not supported.', $key));
        }

        return file_get_contents($file);
    }

    protected function getFormat()
    {
        return 'xml';
    }
}

class ExternalEntityTest
{
    /** @Type("string") @XmlValue */
    public $foo;
}

