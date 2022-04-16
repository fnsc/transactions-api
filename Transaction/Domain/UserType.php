<?php

namespace Transaction\Domain;

enum UserType: string
{
    case REGULAR = 'regular';

    case SELLER = 'seller';
}
