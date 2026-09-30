<?php

namespace App\Support;

class EnvFile
{
    public static function set(array $data, array $unquotedKeys = []): bool
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath) || !is_writable($envPath)) {
            return false;
        }

        $env = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $line = in_array($key, $unquotedKeys, true)
                ? $key . '=' . $value
                : $key . '="' . str_replace('"', '\\"', (string) $value) . '"';

            if (preg_match('/^' . preg_quote($key, '/') . '=.*/m', $env)) {
                $env = preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $line, $env);
            } else {
                $env .= PHP_EOL . $line;
            }
        }

        return file_put_contents($envPath, $env) !== false;
    }
}
