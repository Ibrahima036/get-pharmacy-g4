<?php

namespace App\Enum;

enum OrderStatus: string
{
  case InProgress = 'En cours';
  case Delivered = 'Delivré';
  case Cancelled = 'Annulé';
}
