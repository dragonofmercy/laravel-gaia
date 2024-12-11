<?php
namespace Demeter\User;

enum FlashMessageType : string
{
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case ERROR = 'danger';
    case INFORMATION = 'info';
}