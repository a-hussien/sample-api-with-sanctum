<?php

namespace App\Enums;

enum TaskPriority: string  // emun will return a value of string
{
    case Low = "Low";
    case Medium = "Medium";
    case High = "High";
}
