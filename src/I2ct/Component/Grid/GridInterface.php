<?php
namespace I2ct\Component\Grid;

use I2ct\Component\Grid\DataSource\DataSourceInterface;

/**
 * Interface DataGridInterface
 *
 * @package I2ct\Component\Grid
 * @author  Octavian Matei <octav@octav.name>
 * @since   13.09.2016
 */
interface GridInterface
{
    /**
     * Return the filters
     *
     * @return array
     */
    public function getFilters();

    /**
     * Get the processed grid data
     *
     * @return array
     */
    public function getGridData();

    /**
     * Return the pagination
     *
     * @return array
     */
    public function getPagination();

    /**
     * Return the sorters
     *
     * @return array
     */
    public function getSorters();

    /**
     * Extract request params
     *
     * @param array $params
     *
     * @return mixed
     */
    public function parseParams($params);

    /**
     * Process the data from the data source
     *
     * @return self
     */
    public function processData();

    /**
     * Set the data source
     *
     * @param \I2ct\Component\Grid\DataSource\DataSourceInterface $dataSource
     *
     * @return self
     */
    public function setDataSource(DataSourceInterface $dataSource);

    /**
     * Resolve the query
     *
     * @param \Zend_Db_Select                                     $select
     * @param \I2ct\Component\Grid\DataSource\DataSourceInterface $dataSource
     *
     * @return mixed
     */
    public function setupDataSource($select, DataSourceInterface $dataSource);
}
