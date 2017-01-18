<?php
/**
 * @author      Tsvetan Stoychev <ceckoslab@gmail.com>
 * @website     http://www.ceckoslab.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */

namespace Twinsen\QuickLogin\Model\Source;

class AdminUsername implements \Magento\Framework\Option\ArrayInterface
{

    /** @var array */
    protected $_options = array();
    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    private $collection;

    public function __construct(
        \Magento\User\Model\ResourceModel\User\Collection $collection
    )
    {

        $this->collection = $collection;
    }

    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $users = $this->collection
                ->addFieldToFilter('is_active', array('eq' => 1))
                ->addFieldToSelect(array('username'))
                ->load();

            foreach ($users as $user) {
                /** @var \Magento\User\Model\ResourceModel\User $user */
                $this->_options[] = array('value' => $user->getUsername(), 'label' => $user->getUsername());
            }
        }

        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, array('value' => '', 'label' => __('--Please Select--')));
        }

        return $options;
    }
}
