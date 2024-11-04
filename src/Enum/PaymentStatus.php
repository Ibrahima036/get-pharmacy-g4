<?php

namespace App\Enum;

enum PaymentStatus: string
{
  case Paid = 'payé';
  case Pending = 'en attente';
  case Cancelled = 'Annulé';
}
