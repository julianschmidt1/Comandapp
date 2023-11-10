<?php
interface IApiUsable
{
    public function Create($request, $response, $args);
    public function GetById($request, $response, $args);
    public function GetAll($request, $response, $args);
    public function Update($request, $response, $args);
    public function Delete($request, $response, $args);
}