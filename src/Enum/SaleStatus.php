<?php

namespace App\Enum;

enum SaleStatus: string
{
  case Paid = 'payé';
  case Pending = 'en attente';
  case Cancelled = 'Annulé';
}
