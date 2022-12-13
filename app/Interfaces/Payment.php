<?php

namespace App\Interfaces;

interface Payment
{
   public function makePayment($amount, $walletID);
   public function verifyPayment($ref , $email);
}
