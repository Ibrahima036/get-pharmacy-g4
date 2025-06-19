<?php

namespace App\Enum;
enum PharmaceuticalFormType :string
{
    case  Tablet  = 'Comprimé';
    case Capsule = 'Capsule';
    case Syrup = 'Sirop';
}