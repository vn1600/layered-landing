<?php
 
class Hackathon_Layeredlanding_Model_Layeredlanding extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'layered_landing';

    public function _construct()
    {
        parent::_construct();
        $this->_init('layeredlanding/layeredlanding');
    }
	
	public function getAttributes()
	{
		return Mage::getModel('layeredlanding/attributes')->getCollection()
					->addFieldToFilter('layeredlanding_id', $this->getId());
	}

    public function loadByUrl($url)
    {
        $collection = $this->getCollection()
            ->addFieldToSelect('layeredlanding_id')
            ->addFieldToSelect('store_ids')
            ->addFieldToFilter('page_url', array('eq' => $url));

        if ($collection->getSize()) 
		{
			$store_ids = explode(',', $collection->getFirstItem()->getStoreIds());
			if (in_array(Mage::app()->getStore()->getId(), $store_ids) || in_array('0', $store_ids)) // check if the item applies to the store or to system level
			{
				$this->load($collection->getFirstItem()->getId());
			}
        }

        $resource = Mage::getModel('core/resource');
        $db = $resource->getConnection('core_write');

	$tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        $attributes = $db->fetchPairs("SELECT attribute_id,value FROM {$tablePrefix}layeredlanding_attributes WHERE layeredlanding_id = ?", array($this->getId()));
        if($attributes) {
            $this->setData('layered_attributes', $attributes);
        }

        return $this;
    }

    public function getUrl()
    {
        return Mage::getUrl().$this->getPageUrl();
    }
}
