<?php
namespace BretRZaun\ConfigProvider;

use Silex\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Silex-Provider for application config in YAML format
 *
 */
class ConfigServiceProvider implements ServiceProviderInterface
{

    /**
     * @var string
     */
    protected $configfile;

    /**
     * @var array
     */
    protected $replacements = [];

    /**
     * ConfigServiceProvider constructor.
     *
     * @param string $configFile config file
     * @param array $replacements list of placeholders
     */
    public function __construct($configFile, array $replacements = [])
    {
        $this->configfile = $configFile;

        if ($replacements) {
            foreach ($replacements as $key => $value) {
                $this->replacements['%'.$key.'%'] = $value;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function register(Container $app)
    {
        if (!file_exists($this->configfile)) {
            throw new \RuntimeException("Config file {$this->configfile} not found.");
        }
        $config = Yaml::parse(file_get_contents($this->configfile));
        foreach ($config as $key => $value) {
            $app[$key] = $value;
        }

        $this->merge($app, $config);
    }

    /**
     * @param Container $app
     * @param array     $config
     *
     * @return void
     */
    private function merge(Container $app, array $config)
    {
        foreach ($config as $key => $value) {
            if (isset($app[$key]) && is_array($value)) {
                $app[$key] = $this->mergeRecursively($app[$key], $value);
            } else {
                $app[$key] = $this->doReplacements($value);
            }
        }
    }

    /**
     * @param array $currentValue
     * @param array $newValue
     *
     * @return array
     */
    private function mergeRecursively(array $currentValue, array $newValue)
    {
        foreach ($newValue as $name => $value) {
            if (is_array($value) && isset($currentValue[$name])) {
                $currentValue[$name] = $this->mergeRecursively($currentValue[$name], $value);
            } else {
                $currentValue[$name] = $this->doReplacements($value);
            }
        }
        return $currentValue;
    }

    /**
     * @param $value
     *
     * @return array|string
     */
    private function doReplacements($value)
    {
        if (!$this->replacements) {
            return $value;
        }
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doReplacements($v);
            }
            return $value;
        }
        if (is_string($value)) {
            return strtr($value, $this->replacements);
        }
        return $value;
    }
}
