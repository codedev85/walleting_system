<?php

namespace App\Interfaces;

interface CashoutInterface
{
    public function verifyBank($amount , $accountNumber);
}


