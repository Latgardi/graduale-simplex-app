<?php
namespace App\Lib\Type;
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
class ControllerResult
{
    public function __construct(
        private array $data = []
    ) {}

    public function get(string $key): mixed
    {
        return $this->data[$key];
    }

    public function isSet(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function hasEmpty(string $key): bool
    {
        return empty($this->data[$key]);
    }

    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
