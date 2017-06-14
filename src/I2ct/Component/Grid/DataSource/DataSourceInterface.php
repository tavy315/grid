<?php
namespace I2ct\Component\Grid\DataSource;

/**
 * Interface DataSourceInterface
 *
 * @package I2ct\Component\Grid\DataSource
 * @author  Octavian Matei <octav@octav.name>
 * @since   13.09.2016
 */
interface DataSourceInterface
{
    /**
     * Current page number
     *
     * @return int
     */
    public function getCurrentPageNumber();

    /**
     * Get the data from the source
     *
     * @return array
     */
    public function getData();

    /**
     * Total pages
     *
     * @return int
     */
    public function getTotalItemCount();

    /**
     * @param mixed $select
     * @param array $pagination
     */
    public function setup($select, $pagination = [ 'length' => 10, 'page' => 1 ]);
}
