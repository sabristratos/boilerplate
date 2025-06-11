<?php

namespace App\Enums;

enum SettingType: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case SELECT = 'select';
    case MULTISELECT = 'multiselect';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case COLOR = 'color';
    case FILE = 'file';
    case NUMBER = 'number';
    case EMAIL = 'email';
    case URL = 'url';
    case PASSWORD = 'password';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case TIME = 'time';
    case BOOLEAN = 'boolean';
    case JSON = 'json';

    /**
     * Get all available setting types as an array.
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_column(self::cases(), 'value')
        );
    }

    /**
     * Get a human-readable name for the setting type.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::TEXT => 'Text',
            self::TEXTAREA => 'Text Area',
            self::SELECT => 'Select',
            self::MULTISELECT => 'Multi-Select',
            self::CHECKBOX => 'Checkbox',
            self::RADIO => 'Radio',
            self::COLOR => 'Color',
            self::FILE => 'File',
            self::NUMBER => 'Number',
            self::EMAIL => 'Email',
            self::URL => 'URL',
            self::PASSWORD => 'Password',
            self::DATE => 'Date',
            self::DATETIME => 'Date & Time',
            self::TIME => 'Time',
            self::BOOLEAN => 'Boolean',
            self::JSON => 'JSON',
        };
    }
}
