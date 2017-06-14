<?php
namespace I2ct\Component\Grid\DataFilter;

/**
 * Interface DataFilterInterface
 *
 * @package I2ct\Component\Grid\DataFilter
 * @author  Octavian Matei <octav@octav.name>
 * @since   12.10.2016
 */
interface DataFilterInterface
{
    /**
     * @param mixed $tableSelect
     * @param array $filters
     *
     * @return mixed
     */
    public function applyFilters($tableSelect, array $filters);
}
