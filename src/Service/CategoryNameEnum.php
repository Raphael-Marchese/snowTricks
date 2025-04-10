<?php

namespace App\Service;

enum CategoryNameEnum: string
{
    case GRABS = 'grabs';
    case ROTATIONS = 'rotations';
    case FLIPS = 'flips';
    case SLIDES = 'slides';
    case OFF_AXIS_ROTATIONS = 'off-axis rotation';
    case ONE_FOOT_TRICKS = 'one foot tricks';
    case DEFAULT = 'Default';

}
