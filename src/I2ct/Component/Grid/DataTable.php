<?php
namespace I2ct\Component\Grid;

use I2ct\Component\Grid\DataFilter\DataFilterInterface;
use I2ct\Component\Grid\DataFilter\ZendDataFilter;
use I2ct\Component\Grid\DataSource\DataSourceInterface;
use I2ct\Component\Grid\DataSource\ZendDataSource;
use I2ct\Component\Grid\Traits\GridTrait;
use Salaries\Model\Payment\PaymentQuery;

/**
 * Class DataGrid
 *
 * @package I2ct\Component\Grid
 * @author  Octavian Matei <octav@octav.name>
 * @since   13.09.2016
 */
class DataTable implements GridInterface
{
    use GridTrait;

    /** @var array */
    protected $config = [];

    protected $mappedParams = null;

    protected $columnParams = null;
    protected $filterParams = null;
    protected $sortParams = null;
    protected $paginationParams = null;

    /** @var \I2ct\Component\Grid\DataSource\DataSourceInterface */
    protected $dataSource = null;

    protected $gridData = null;

    /** @var array */
    protected $gridFieldsOrder = [];

    /**
     * Create DataTable Instance
     *
     * @param array $config
     *
     * @return self
     */
    public function __construct($config = [])
    {
        return $this->setConfig($config);
    }

    /**
     * Return the columns
     *
     * @return array
     */
    public function getColumns()
    {
        $this->extractColumnsParams();

        return $this->columnParams;
    }

    /**
     * Return the filters
     *
     * @return array
     */
    public function getFilters()
    {
        $this->extractFilterParams();

        return $this->filterParams;
    }

    /**
     * Get the processed grid data
     *
     * @return array
     */
    public function getGridData()
    {
        $this->processData();

        return $this->gridData;
    }

    /**
     * Return the pagination
     *
     * @return array
     */
    public function getPagination()
    {
        $this->extractPaginationParams();

        return $this->paginationParams;
    }

    /**
     * Return the sorters
     *
     * @return array
     */
    public function getSorters()
    {
        $this->extractSortingParams();

        return $this->sortParams;
    }

    /**
     * Extract the information needed by the grid
     *
     * @param array $params
     *
     * @return self
     */
    public function parseParams($params)
    {
        $this->mappedParams = $params;

        $this->extractColumnsParams()
             ->extractPaginationParams()
             ->extractFilterParams()
             ->extractSortingParams();

        return $this;
    }

    /**
     * Process the data from the data source
     *
     * @return self
     */
    public function processData()
    {
        if (!$this->gridData) {
            $this->processGridFieldsOrderSettings();

            $this->gridData = [
                'data'            => $this->normalizeData(),
                'draw'            => isset($this->mappedParams['draw']) ? (int) $this->mappedParams['draw'] : 1,
                'page'            => (int) $this->dataSource->getCurrentPageNumber(),
                'recordsFiltered' => (int) $this->dataSource->getTotalItemCount(),
                'recordsTotal'    => (int) $this->dataSource->getTotalItemCount(),
            ];
        }

        return $this;
    }

    /**
     * @param array $config
     *
     * @return self
     */
    public function setConfig($config)
    {
        if (is_array($config)) {
            $this->config = $config;
        }

        return $this;
    }

    /**
     * Set the data source
     *
     * @param \I2ct\Component\Grid\DataSource\DataSourceInterface $dataSource
     *
     * @return self
     */
    public function setDataSource(DataSourceInterface $dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    /**
     * @param mixed                                               $select
     * @param \I2ct\Component\Grid\DataFilter\DataFilterInterface $dataFilter
     *
     * @return self
     */
    public function setupDataFilters($select, DataFilterInterface $dataFilter = null)
    {
        if ($dataFilter === null) {
            $dataFilter = new ZendDataFilter();
        }

        $dataFilter->applyFilters($select, $this->getFilters());

        return $this;
    }

    /**
     * @param string $section
     * @param string $gridName
     *
     * @return self
     */
    public function setupDataGrid($section, $gridName)
    {
        /* @see \I2ct\Component\Grid\Traits\GridTrait::getGridConfig() */
        $config = $this->getGridConfig($section, $gridName);

        return $this->setConfig($config);
    }

    /**
     * @param mixed $select
     *
     * @return self
     */
    public function setupDataSorters($select)
    {
        foreach ($this->getSorters() as $sortName => $sortOrder) {
//            if (!in_array($sortName, $this->getColumns())) {
//                continue;
//            }
            if ($select instanceof \Zend_Db_Select) {
                $select->order([ sprintf('%s %s', $sortName, $sortOrder) ]);
            }
            if ($select instanceof \ModelCriteria) {
                $select->orderBy($sortName, $sortOrder);
            }
        }

        return $this;
    }

    /**
     * Create paginator
     *
     * @param mixed                                               $select
     * @param \I2ct\Component\Grid\DataSource\DataSourceInterface $dataSource
     *
     * @return self
     */
    public function setupDataSource($select, DataSourceInterface $dataSource = null)
    {
        if ($dataSource === null) {
            $dataSource = new ZendDataSource();
        }

        $dataSource->setup($select, $this->getPagination());

        return $this->setDataSource($dataSource);
    }

    /**
     * Extract columns params
     *
     * @return self
     */
    protected function extractColumnsParams()
    {
        if (!$this->columnParams) {
            $columns = [];

            if (isset($this->mappedParams['columns'])) {
                $columns = $this->mappedParams['columns'];
            }

            foreach ($columns as $columnId => $columnData) {
                // remove unused columns
                if ($columnData['name'] !== '') {
                    $this->columnParams[$columnId] = $columnData['name'];
                }
            }
        }

        return $this;
    }

    /**
     * Extract filter params
     *
     * @return self
     */
    protected function extractFilterParams()
    {
        if (!$this->filterParams) {
            $filters = [];

            if (isset($this->mappedParams['filters'])) {
                $filters = $this->mappedParams['filters'];
            }

            foreach ($filters as $filterName => $filterValue) {
                // remove unused filters
                if (strlen(trim($filterValue)) == 0 || $filterValue == '') {
                    unset($filters[$filterName]);
                }
            }

            $this->filterParams = $filters;
        }

        return $this;
    }

    /**
     * Extract pagination params from the request
     *
     * @return self
     */
    protected function extractPaginationParams()
    {
        if (!$this->paginationParams) {
            $start = isset($this->mappedParams['start']) ? (int) $this->mappedParams['start'] : 0;
            $length = isset($this->mappedParams['length']) ? (int) $this->mappedParams['length'] : 10;

            $this->paginationParams = [
                'page'   => $start / $length + 1,
                'length' => $length,
            ];
        }

        return $this;
    }

    /**
     * Extract sorting params
     *
     * @return self
     */
    protected function extractSortingParams()
    {
        if (!$this->sortParams) {
            $sortParams = [];
            $sorters = [];

            if (isset($this->mappedParams['order'])) {
                $sorters = $this->mappedParams['order'];
            }

            foreach ($sorters as $sorter) {
                $orderColumn = isset($sorter['column']) ? $sorter['column'] : 0;
                $orderDir = isset($sorter['dir']) ? $sorter['dir'] : 'asc';

                if (isset($this->columnParams[$orderColumn])) {
                    $sortParams[$this->columnParams[$orderColumn]] = $orderDir;
                }
            }

            $this->sortParams = $sortParams;
        }

        return $this;
    }

    /**
     * Transform Paginated data to DataTable compatible data structure
     *
     * @return array
     */
    protected function normalizeData()
    {
        $normalizedData = [];

        foreach ($this->dataSource->getData() as $row) {
            $rowSet = [];
            foreach ($row as $field => $value) {
                // skip items that are not defined
                if (!isset($this->config['fields']['settings'][$field])) {
                    continue;
                }
                // assign order in the result array
                $rowSet[$this->gridFieldsOrder[$field]] = $value;
            }

            // add blank entry for actions
            $rowSet[] = null;

            ksort($rowSet);
            $normalizedData[] = $rowSet;
        }

        return $normalizedData;
    }

    /**
     * Process grid fields order
     *
     * @return self
     */
    protected function processGridFieldsOrderSettings()
    {
        if (!count($this->gridFieldsOrder)) {
            $definedFieldsOrder = array_flip($this->config['fields']['order']);
            $maxOrderIdx = max($definedFieldsOrder);

            // add order definition for fields which didn't had the order defined
            foreach ($this->config['fields']['settings'] as $fieldName => $settings) {
                if (!isset($definedFieldsOrder[$fieldName])) {
                    $definedFieldsOrder[$fieldName] = ++$maxOrderIdx;
                }
            }

            $this->gridFieldsOrder = $definedFieldsOrder;
        }

        return $this;
    }
}
