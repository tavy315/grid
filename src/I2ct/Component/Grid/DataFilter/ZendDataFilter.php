<?php
namespace I2ct\Component\Grid\DataFilter;

/**
 * Class ZendDataFilter
 *
 * @package I2ct\Component\Grid\DataFilter
 * @author  Octavian Matei <octav@octav.name>
 * @since   12.10.2016
 */
class ZendDataFilter implements DataFilterInterface
{
    /**
     * @param \Zend_Db_Select $tableSelect
     * @param array           $filters
     *
     * @return \Zend_Db_Select
     */
    public function applyFilters($tableSelect, array $filters)
    {
        foreach ($filters as $filterKey => $filterValue) {
            $tableSelect->where($filterKey . ' LIKE ?', '%' . $filterValue . '%');
        }

        return $tableSelect;
    }
}
