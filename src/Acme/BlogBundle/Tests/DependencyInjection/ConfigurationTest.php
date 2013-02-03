<?php

namespace Acme\BlogBundle\Tests\DependencyInjection;

use Acme\BlogBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider configurationProvider
     * @param array $actualConfig
     * @param array $processedConfig
     */
    public function testConfiguration($actualConfig, $processedConfig)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $this->assertEquals($processedConfig, $processor->processConfiguration($configuration, $actualConfig));
    }

    public function configurationProvider()
    {
        return array(
            'defaults' => array(
                array(),
                array(
                    'doctrine' => array(
                        'type' => 'doctrine_orm',
                        'manager_name' => 'default'
                    )
                )
            ),
            'empty doctrine' => array(
                array(
                    array(
                        'doctrine' => array()
                    )
                ),
                array(
                    'doctrine' => array(
                        'type' => 'doctrine_orm',
                        'manager_name' => 'default'
                    )
                )
            ),
            'custom type' => array(
                array(
                    array(
                        'doctrine' => array(
                            'type' => 'doctrine_mongodb'
                        )
                    )
                ),
                array(
                    'doctrine' => array(
                        'type' => 'doctrine_mongodb',
                        'manager_name' => 'default'
                    )
                )
            ),
            'custom manager_name' => array(
                array(
                    array(
                        'doctrine' => array(
                            'manager_name' => 'custom'
                        )
                    )
                ),
                array(
                    'doctrine' => array(
                        'type' => 'doctrine_orm',
                        'manager_name' => 'custom'
                    )
                )
            )
        );
    }

    /**
     * @dataProvider configurationErrorsProvider
     * @param array $actualConfig
     * @param $errorMessage
     * @param string $errorMessage
     */
    public function testConfigurationErrors($actualConfig, $errorMessage)
    {
        $this->setExpectedException(
            'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
            $errorMessage
        );
        $processor = new Processor();
        $configuration = new Configuration();
        $processor->processConfiguration($configuration, $actualConfig);
    }

    public function configurationErrorsProvider()
    {
        return array(
            'invalid type' => array(
                array(
                    array(
                        'doctrine' => array(
                            'type' => 'mysql'
                        )
                    )
                ),
                'Invalid configuration for path "acme_blog.doctrine.type": Invalid type "mysql".'
            ),
            'empty type' => array(
                array(
                    array(
                        'doctrine' => array(
                            'type' => null
                        )
                    )
                ),
                'The path "acme_blog.doctrine.type" cannot contain an empty value, but got null.'
            ),
            'empty manager_name' => array(
                array(
                    array(
                        'doctrine' => array(
                            'manager_name' => null
                        )
                    )
                ),
                'The path "acme_blog.doctrine.manager_name" cannot contain an empty value, but got null.'
            )
        );
    }
}
