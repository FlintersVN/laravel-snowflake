<?php

namespace Septech\Snowflake\Console\Commands;

class Env
{
    protected $filepath;

    protected $attributes = [];

    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    public function get($key, $default = null)
    {
        return env($key, $default);
    }

    public function has($key)
    {
        return env($key, $this) !== $this;
    }

    public function replace($key, $value)
    {
        $replacement = sprintf("/^\s*($key)\s*=\s*(.*)?\s*$/m", $key);

        file_put_contents($this->filepath, preg_replace(
            $replacement,
            $key.'='.$value,
            file_get_contents($this->filepath)
        ));
    }

    public function put($key, $value)
    {
        $this->has($key) ? $this->replace($key, $value) : $this->write($key, $value);
    }

    public function write($key, $value)
    {
        file_put_contents($this->filepath, sprintf("%s=%s\n", $key, $value), FILE_APPEND);
    }
}