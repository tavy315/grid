<?php
namespace I2ct\Component\Grid\DataSource;

use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class DoctrineDataSource.
 *
 * @package I2ct\Component\Grid\DataSource
 * @author  Octavian Matei <octav@octav.name>
 * @since   03.02.2018
 */
class DoctrineDataSource implements DataSourceInterface
{
    /** @var \Zend_Paginator */
    protected $paginator;

    /** @var array */
    protected $data;

    /** @var int */
    protected $length;

    /** @var */
    protected $page;

    /**
     * Current page number
     *
     * @return int
     */
    public function getCurrentPageNumber()
    {
        return $this->page;
    }

    /**
     * Get the data from the source
     *
     * @return array
     */
    public function getData()
    {
        $this->paginator->getQuery()
                        ->setFirstResult(($this->page - 1) * $this->length)
                        ->setMaxResults($this->length);

        return $this->paginator->getIterator()->getArrayCopy();
    }

    /**
     * Total pages
     *
     * @return int
     */
    public function getTotalItemCount()
    {
        return $this->paginator->count();
    }

    /**
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $select
     * @param array                                          $pagination
     */
    public function setup($select, $pagination = [ 'length' => 10, 'page' => 1 ])
    {
        $this->length = (int) $pagination['length'];
        $this->page = (int) $pagination['page'];
        $this->paginator = new Paginator($select);
    }
}
