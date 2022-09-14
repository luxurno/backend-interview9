<?php

namespace App\Messenger\Message;

class EmailMessage
{
    public function __construct(
        public int $id,
    ) { }

    public function getId(): int
    {
        return $this->id;
    }
}
