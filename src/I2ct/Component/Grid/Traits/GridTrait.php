<?php
namespace I2ct\Component\Grid\Traits;

use Symfony\Component\Yaml\Yaml;

/**
 * GridTrait
 *
 * @package I2ct\Component\Grid\Traits
 * @author  Octavian Matei <octav@octav.name>
 * @since   18.09.2016
 */
trait GridTrait
{
    private static $filename = 'grids.yml';

    /**
     * Get grid parameters for given section and grid name
     *
     * By default, we only need the first 2 parameters, ex: ('admin', 'labels'),
     * but we can also use the extended version in case of grouping several grids
     * under same controller. In this case we need the third parameter as $gridName
     * and second parameter as group name, ex: ('admin', 'features_import', 'features')
     *
     * @param string $section  Section/Module name (ex: admin)
     * @param string $gridName Grid name (ex: labels)
     *
     * @throws \Exception
     * @return array
     */
    public function getGridConfig($section, $gridName)
    {
        if (file_exists(ROOT . '/config/' . self::$filename) === false) {
            throw new \Exception('Could not find the ' . self::$filename . ' file');
        }

        $grids = Yaml::parse(file_get_contents(ROOT . '/config/' . self::$filename));

        if (!isset($grids[$section][$gridName])) {
            throw new \Exception(sprintf('Could not load grid configuration for [%s][%s]', $section, $gridName));
        }

        return $grids[$section][$gridName];
    }
}
