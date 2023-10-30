<?php

declare(strict_types=1);

namespace App\Infrastructure\ValueObject\String;

abstract readonly class NonEmptyStringLiteral implements \JsonSerializable, \Stringable, StringLiteral
{
    public function __construct(
        private string $string,
    ) {
        $this->guardNonEmpty($string);
    }

    public function __toString(): string
    {
        return $this->string;
    }

    public static function fromString(string $string): static
    {
        return new static($string);
    }

    public static function fromOptionalString(?string $string = null): ?static
    {
        if (!$string) {
            return null;
        }

        return new static($string);
    }

    public function jsonSerialize(): string
    {
        return (string)$this;
    }

    private function guardNonEmpty(string $string): void
    {
        if (empty($string)) {
            throw new \InvalidArgumentException(static::class . " can not be empty");
        }
    }
}
