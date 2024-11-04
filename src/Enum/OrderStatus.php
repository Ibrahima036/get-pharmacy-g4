<?php

namespace App\Enum;

enum OrderStatus: string
{
  case InProgress = 'en cours';
  case Delivered = 'delivré';
  case Cancelled = 'Annulé';
}
