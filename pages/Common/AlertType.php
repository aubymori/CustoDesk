<?php
namespace CustoDesk\Page\Common;

enum AlertType : string
{
    case NORMAL  = "Normal";
    case ERROR   = "Error";
    case WARNING = "Warning";
}