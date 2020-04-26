<?php
Class Identite{
  public $userId;
  public $pseudo;

  public function __construct($userId,$pseudo){
    $this->userId = $userId;
    $this->pseudo = $pseudo;
  }
}
?>
