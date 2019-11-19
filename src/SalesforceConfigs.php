<?php


namespace Twobes\Salesforce;

class SalesforceConfigs
{
    protected static $settings = [
        'tokenLifeTime' => 5400,
        'defaultVersion' => "v20.0",
        'httpRequestVerboseLogFile' => false,
        'defaultAuthenticator' => null
    ];

    /**
     * Global settings
     * @param array $settings
     */
    public static function globalSetup($settings)
    {
        self::$settings = array_merge(self::$settings, $settings);
    }

    /**
     * Get settings
     * @param string $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public static function getSetting($name, $default = null)
    {
        return isset(self::$settings[$name]) ? self::$settings[$name] : $default;
    }
}