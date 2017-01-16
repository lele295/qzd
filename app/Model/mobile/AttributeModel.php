<?php

namespace App\Model\mobile;


class AttributeModel
{
    public static function formatAttributes($object, $labels)
    {
        $result = [];
        foreach ($labels as $attribute => $label) {
            $result[$attribute]['value'] = isset($object->$attribute) ? $object->$attribute : '';
            $result[$attribute]['label'] = $label;
        }
        return $result;
    }
}
