<?php

function assets(string $path): string
{
    return "../assets/" . ltrim($path, '');
}