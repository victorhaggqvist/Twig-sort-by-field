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

                $sorted_a = $a;
                $sorted_b = $b;

                foreach (explode('.', $sort_by) as $sort_by) {

                    if (is_array($sorted_a)) {
                        $a_sort_value = $sorted_a[$sort_by];
                    } else {
                        if (method_exists($sorted_a, 'get'.ucfirst($sort_by))) {
                            $a_sort_value = $sorted_a->{'get'.ucfirst($sort_by)}();
                        } else {
                            $a_sort_value = $sorted_a->$sort_by;
                        }
                    }

                    if (is_array($b)) {
                        $b_sort_value = $sorted_b[$sort_by];
                    } else {
                        if (method_exists($sorted_b, 'get'.ucfirst($sort_by))) {
                            $b_sort_value = $sorted_b->{'get'.ucfirst($sort_by)}();
                        } else {
                            $b_sort_value = $sorted_b->$sort_by;
                        }
                    }

                    $sorted_a = $a_sort_value;
                    $sorted_b = $b_sort_value;
                }

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

        $is_sortable = false;
        $sorted_item = $item;

        foreach (explode('.', $field) as $field) {
            if (is_array($sorted_item)) {
                $is_sortable = array_key_exists($field, $sorted_item);
                if ($is_sortable) {
                    $sorted_item = $sorted_item[$field];
                }
            } elseif (is_object($item)) {
                $is_sortable = isset($sorted_item->$field) || method_exists($sorted_item, 'get'.ucfirst($field));
                if ($is_sortable) {
                    $sorted_item = isset($sorted_item->$field) ? $sorted_item->$field : (method_exists($sorted_item, 'get'.ucfirst($field)) ? $sorted_item->{'get'.ucfirst($field)}() : $sorted_item);
                }
            }
        }

        return $is_sortable;
    }
}
