<?php
namespace CustoDesk\Page\Common;

class Alert
{
    function __construct(
        public AlertType $type,
        public string $text,
        public bool $dismissible = false,
    )
    {}
}