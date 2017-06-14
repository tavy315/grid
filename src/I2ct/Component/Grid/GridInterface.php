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
     * Extract request params
     *
     * @param array $params
     *
     * @return mixed
     */
    public function parseParams($params);

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

    /**
     * Process the data from the data source
     *
     * @return self
     */
    public function processData();

    /**
     * Get the processed grid data
     *
     * @return array
     */
    public function getGridData();

    /**
     * Return the filters
     *
     * @return array
     */
    public function getFilters();

    /**
     * Return the sorters
     *
     * @return array
     */
    public function getSorters();

    /**
     * Return the pagination
     *
     * @return array
     */
    public function getPagination();
}
