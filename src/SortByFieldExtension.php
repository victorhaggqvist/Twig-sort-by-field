<?php

namespace Snilius\Twig;

use Exception;

/**
 * User: Victor Häggqvist
 * Date: 3/4/15
 * Time: 2:07 AM
 *
 * The base of the filter is borrowed from https://github.com/dannynimmo/craftcms-sortbyfield
 *
 * I have extended it to also sort array structures
 */
class SortByFieldExtension extends \Twig_Extension {

    public function getName() {
        return 'sortbyfield';
    }

    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('sortbyfield', array($this, 'sortByFieldFilter'))
        );
    }

    /**
     * The "sortByField" filter sorts an array of entries (objects or arrays) by the specified field's value
     *
     * Usage: {% for entry in master.entries|sortbyfield('ordering', 'desc') %}
     */
    public function sortByFieldFilter($content, $sort_by = null, $direction = 'asc') {

        if (is_a($content, 'Doctrine\Common\Collections\Collection')) {
            $content = $content->toArray();
        }

        if (!is_array($content)) {
            throw new \InvalidArgumentException('Variable passed to the sortByField filter is not an array');
        } elseif (count($content) < 1) {
            return $content;
        } elseif ($sort_by === null) {
            throw new Exception('No sort by parameter passed to the sortByField filter');
        } elseif (!self::isSortable(current($content), $sort_by)) {
            throw new Exception('Entries passed to the sortByField filter do not have the field "' . $sort_by . '"');
        } else {
            // Unfortunately have to suppress warnings here due to __get function
            // causing usort to think that the array has been modified:
            // usort(): Array was modified by the user comparison function
            @usort($content, function ($a, $b) use ($sort_by, $direction) {
                $flip = ($direction === 'desc') ? -1 : 1;

                if (is_array($a))
                    $a_sort_value = $a[$sort_by];
                else if (method_exists($a, 'get' . ucfirst($sort_by)))
                    $a_sort_value = $a->{'get' . ucfirst($sort_by)}();
                else
                    $a_sort_value = $a->$sort_by;

                if (is_array($b))
                    $b_sort_value = $b[$sort_by];
                else if (method_exists($b, 'get' . ucfirst($sort_by)))
                    $b_sort_value = $b->{'get' . ucfirst($sort_by)}();
                else
                    $b_sort_value = $b->$sort_by;

                if ($a_sort_value == $b_sort_value) {
                    return 0;
                } else if ($a_sort_value > $b_sort_value) {
                    return (1 * $flip);
                } else {
                    return (-1 * $flip);
                }
            });
        }
        return $content;
    }

    /**
     * Validate the passed $item to check if it can be sorted
     * @param $item mixed Collection item to be sorted
     * @param $field string
     * @return bool If collection item can be sorted
     */
    private static function isSortable($item, $field) {
        if (is_array($item))
            return array_key_exists($field, $item);
        elseif (is_object($item))
            return isset($item->$field) || property_exists($item, $field);
        else
            return false;
    }
}
