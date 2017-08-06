<?php
namespace I2ct\Component\Grid\DataSource;

/**
 * Class PropelDataSource
 *
 * @package I2ct\Component\Grid\DataSource
 * @author  Octavian Matei <octav@octav.name>
 * @since   06.08.2017
 */
class PropelDataSource implements DataSourceInterface
{
    /**
     * @var \PropelModelPager
     */
    protected $pager = false;

    /**
     * @var array
     */
    protected $data;

    /**
     * Current page number
     *
     * @return int
     */
    public function getCurrentPageNumber()
    {
        return $this->pager->getPage();
    }

    /**
     * Get the data from the source
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->data) {
            $this->data = $this->pager->getResults()->toArray(null, false, \BasePeer::TYPE_FIELDNAME);
        }

        return $this->data;
    }

    /**
     * Total pages
     *
     * @return int
     */
    public function getTotalItemCount()
    {
        return $this->pager->getLastPage();
    }

    /**
     * @param \ModelCriteria $select
     * @param array          $pagination
     */
    public function setup($select, $pagination = [ 'length' => 10, 'page' => 1 ])
    {
        $this->pager = $select->paginate($pagination['page'], $pagination['length']);
    }
}
