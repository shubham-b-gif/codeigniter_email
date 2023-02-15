<?php
  namespace App\Models;
  use CodeIgniter\Model;

  class Product extends Model{
    protected $table = 'products';
    protected $primarykey = 'id';
    protected $allowedFields =[
        'product_name',
        'Product_cost',
        'category',
        'country'

    ] ;   
  }
?>