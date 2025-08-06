<?php

enum FormColor: string {
    case BLUE = 'blue';
    case RED = 'red';
    case GREEN = 'green';
    case YELLOW = 'yellow';

    public function getDisplayName(): string {
        return match($this) {
            self::BLUE => 'Blue',
            self::RED => 'Red',
            self::GREEN => 'Green',
            self::YELLOW => 'Yellow',
        };
    }

    public function getCssClass(): string {
        return match($this) {
            self::BLUE => 'bg-primary',
            self::RED => 'bg-danger',
            self::GREEN => 'bg-success',
            self::YELLOW => 'bg-warning',
        };
    }

    public static function getAllColors(): array {
        return [
            self::BLUE,
            self::RED,
            self::GREEN,
            self::YELLOW
        ];
    }
}