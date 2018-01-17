<?php

namespace Framework\Installer;

use function copy;
use function fclose;
use function file_exists;
use function file_get_contents;
use function fwrite;
use function getenv;
use function realpath;
use function str_replace;
use function ucwords;
use const DIRECTORY_SEPARATOR;
use Dotenv\Dotenv;
use Composer\Script\Event;

class PostCreateProject
{
    /**
     * @var string
     */
    private static $fileContents;
    /**
     * @var string
     */
    private static $file;
    
    /**
     * @param Event $event
     */
    public static function buildConfig(Event $event): void
    {
        self::loadEnv();
        self::changeEnvVariable('DB_HOST', self::buildQuestion('Database Host', 'localhost'));
        self::changeEnvVariable('DB_NAME', self::buildQuestion('Database Name', 'tinyfram'));
        self::changeEnvVariable('DB_USER', self::buildQuestion('Database Username', 'root'));
        self::changeEnvVariable('DB_PASWWORD', self::buildQuestion('Database Password'));
        self::changeEnvVariable('MAIL_TO', self::buildQuestion('Mail To', 'admin@mywebsite.com'));
        self::changeEnvVariable('MAIL_FROM', self::buildQuestion('Mail From', 'noreply@mywebsite.com'));
        self::changeEnvVariable('MAIL_SMTP_HOST', self::buildQuestion('Mail SMTP Host', 'localhost'));
        self::changeEnvVariable('MAIL_SMTP_PORT', self::buildQuestion('Mail SMTP Port', 25));
        self::changeEnvVariable('MAIL_SMTP_USER', self::buildQuestion('Mail SMTP Username'));
        self::changeEnvVariable('MAIL_SMTP_PASSWORD', self::buildQuestion('Mail SMTP Password'));
        self::changeEnvVariable('MAIL_AUTH_MODE', self::buildQuestion('Mail SMTP Auth Mode'));
        self::changeEnvVariable('STRIPE_KEY', self::buildQuestion('Stripe Publishable Key'));
        self::changeEnvVariable('STRIPE_SECRET', self::buildQuestion('Stripe Private Key'));
        self::changeEnvVariable('APP_ENV', self::buildQuestion('Application Environment', 'dev'));
        
        self::buildEnvFile();
    }
    
    /**
     * @param string $question
     * @param string $defaultValue
     * @return string
     */
    private static function buildQuestion(string $question, string $defaultValue = ''): string
    {
        echo "\e[1m{$question}\e[0m" . "\e[33m [{$defaultValue}]: \e[0m";
        $handle = fopen('php://stdin', 'rb');
        $result = trim(fgets($handle));
        fclose($handle);
        if (empty($result) && !empty($defaultValue)) {
            return $defaultValue;
        }
        return $result;
    }
    
    private static function buildEnvFile(): void
    {
        $fp = fopen(self::$file, 'wb');
        fwrite($fp, self::$fileContents);
        fclose($fp);
        echo "\n\e[1m\e[42mSettings saved successfully!\e[0m";
    }
    
    /**
     * @param string $env
     * @param string $value
     */
    private static function changeEnvVariable(string $env, string $value): void
    {
        $oldValue           = getenv(ucwords($env));
        self::$fileContents = str_replace(
            ucwords($env) . '=' . $oldValue,
            ucwords($env) . '=' . $value,
            self::$fileContents
        );
    }
    
    private static function loadEnv(): void
    {
        if (!file_exists(realpath('./') . DIRECTORY_SEPARATOR . '.env')) {
            copy('.env-dist', '.env');
        }
        self::$file         = realpath('./') . DIRECTORY_SEPARATOR . '.env';
        self::$fileContents = file_get_contents(self::$file);
        (new Dotenv(realpath('./'), '.env'))->overload();
    }
}
