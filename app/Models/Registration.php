<?php
  namespace App\Models;
  use CodeIgniter\Model;

  class Registration extends Model{
    protected $table = 'registration_user';
    protected $primarykey = 'id';
    protected $allowedFields =[
        'name',
        'dob',
        'email',
        'mobile',
        'address',
        'pincode',
        'password',
        'status',
        'otp',

    ] ;   
  }
?>