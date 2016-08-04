<?php
namespace App\Http\Requests;

interface RequestInterface
{
    public function authorize();
    public function rules();

}