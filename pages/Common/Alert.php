<?php
namespace CustoDesk\Page\Common;

class Alert
{
    function __construct(
        public string $type,
        public string $text
    )
    {}
}