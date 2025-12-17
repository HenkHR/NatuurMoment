<?php

namespace App\Constants;

class GameMode
{
    public const BINGO = 'bingo';
    public const VRAGEN = 'vragen';

    public const MIN_BINGO_ITEMS = 9;
    public const MIN_QUESTIONS = 1;

    public const ALL_MODES = [
        self::BINGO,
        self::VRAGEN,
    ];
}
