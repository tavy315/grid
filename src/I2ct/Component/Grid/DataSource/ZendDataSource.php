<?php
namespace I2ct\Component\Grid\DataSource;

/**
 * Class ZendDataSource
 *
 * @package I2ct\Component\Grid\DataSource
 * @author  Octavian Matei <octav@octav.name>
 * @since   13.09.2016
 */
class ZendDataSource implements DataSourceInterface
{
    /** @var \Zend_Paginator */
    protected $paginator;

    /** @var array */
    protected $data;

    /**
     * @param \Zend_Db_Select $select
     * @param array           $pagination
     */
    public function setup($select, $pagination = [ 'length' => 10, 'page' => 1 ])
    {
        $paginator = \Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($pagination['length']);
        $paginator->setCurrentPageNumber($pagination['page']);

        $this->paginator = $paginator;
    }

    /**
     * Get the data from the source
     *
     * @return array
     */
    public function getData()
    {
        if ($this->data === null) {
            /** @var \Zend_Db_Table_Rowset $items */
            $items = $this->paginator->getCurrentItems();

            $this->data = $items->toArray();
        }

        return $this->data;
    }

    /**
     * Current page number
     *
     * @return int
     */
    public function getCurrentPageNumber()
    {
        return $this->paginator->getCurrentPageNumber();
    }

    /**
     * Total pages
     *
     * @return int
     */
    public function getTotalItemCount()
    {
        return $this->paginator->getTotalItemCount();
    }
}
