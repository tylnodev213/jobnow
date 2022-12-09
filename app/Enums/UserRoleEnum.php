<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class UserRoleEnum extends Enum
{
    public const SUPER_ADMIN = 0;
    public const ADMIN = 1;
    public const APPLICANT = 2;
    public const HR = 3;

    public static function getRolesForRegister(): array
    {
        return [
            'applicant' => self::APPLICANT,
            'hr'        => self::HR,
        ];
    }
}
