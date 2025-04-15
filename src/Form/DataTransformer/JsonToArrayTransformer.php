<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class JsonToArrayTransformer implements DataTransformerInterface
{
    public function transform($array): mixed
    {
        // Convert array to JSON string for the form
        return json_encode($array, JSON_PRETTY_PRINT);
    }

    public function reverseTransform($json): mixed
    {
        // Convert submitted JSON string back to array
        return json_decode($json, true) ?? [];
    }
}