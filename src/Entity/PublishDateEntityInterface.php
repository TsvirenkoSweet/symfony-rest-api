<?php


namespace App\Entity;


interface PublishDateEntityInterface
{
    public function setPublished(\DateTimeInterface $published): PublishDateEntityInterface;
}